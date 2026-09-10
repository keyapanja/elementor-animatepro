/*
 * EAP Custom Cursor.
 *
 * One shared cursor node per page adopts whichever `[data-eap-cc]` element the
 * pointer is currently over, so twenty cursor-enabled elements still cost one
 * node and one rAF loop — and that loop only runs while a cursor is actually
 * being shown.
 *
 * Binding is delegated on the document rather than per element: the config is
 * read at event time, so elements added later (a popup, an AJAX-loaded grid, a
 * Loop Grid page 2) work with no rescan.
 */
(() => {
	if (window.eapCustomCursorBound) {
		return;
	}
	window.eapCustomCursorBound = true;

	const reduced = () =>
		window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	// A custom cursor is meaningless without a pointer to replace.
	const hasHover = () =>
		!window.matchMedia || window.matchMedia('(hover: hover)').matches;

	if (reduced() || !hasHover()) {
		return;
	}

	/*
	 * Per-effect spawn parameters. `rate` is the minimum gap between particles in
	 * ms, `scale` multiplies the author's Trail Size, `spread` is how far a
	 * particle drifts from where it was born, and `life` must stay in step with
	 * the animation duration in custom-cursor.css or nodes would be removed while
	 * still visible.
	 */
	const FX = {
		ink: { rate: 16, scale: 1, spread: 0, life: 900 },
		particles: { rate: 34, scale: 0.4, spread: 46, life: 700 },
		smoke: { rate: 55, scale: 1.7, spread: 30, life: 1400 },
		echo: { rate: 110, scale: 1, spread: 0, life: 900 },
		blocks: { rate: 55, scale: 0.55, spread: 34, life: 800 },
		orbs: { rate: 45, scale: 0.7, spread: 40, life: 1000 },
		frost: { rate: 48, scale: 0.45, spread: 52, life: 900 },
		comet: { rate: 14, scale: 0.6, spread: 0, life: 500 }
	};

	// Beyond this the page is already saturated and more nodes only cost frames.
	const MAX_PARTICLES = 90;

	let root = null;
	let dot = null;

	let host = null;
	let cfg = null;
	let state = '';

	let px = 0;
	let py = 0;
	let rx = 0;
	let ry = 0;
	let seeded = false;

	let raf = 0;
	let lastSpawn = 0;
	let hue = 0;
	let particles = 0;

	const build = () => {
		if (root) {
			return;
		}
		root = document.createElement('div');
		root.className = 'eap-cc-root';
		root.setAttribute('aria-hidden', 'true');

		dot = document.createElement('div');
		dot.className = 'eap-cc-dot';
		root.appendChild(dot);

		document.body.appendChild(root);
	};

	const spawn = (now) => {
		if (!cfg || !cfg.trail || particles >= MAX_PARTICLES) {
			return;
		}

		const fx = FX[cfg.trail.fx] || FX.particles;
		if (now - lastSpawn < fx.rate) {
			return;
		}
		lastSpawn = now;

		const p = document.createElement('div');
		p.className = `eap-cc-p eap-cc-p--${cfg.trail.fx}`;

		const size = (cfg.trail.size || 18) * fx.scale;
		p.style.setProperty('--s', `${size}px`);

		if (fx.spread) {
			const angle = Math.random() * Math.PI * 2;
			const dist = fx.spread * (0.35 + Math.random() * 0.65);
			p.style.setProperty('--tx', `${Math.cos(angle) * dist}px`);
			p.style.setProperty('--ty', `${Math.sin(angle) * dist}px`);
		}

		p.style.setProperty('--r', `${(Math.random() * 2 - 1) * 240}deg`);

		if (cfg.trail.fx === 'orbs') {
			hue = (hue + 11) % 360;
			p.style.setProperty('--h', String(hue));
		}

		// Particles are born at the POINTER, not at the eased cursor position —
		// the trail should mark where the mouse actually went.
		p.style.left = `${px}px`;
		p.style.top = `${py}px`;

		root.appendChild(p);
		particles += 1;

		// animationend is not guaranteed (a background tab can drop it), so the
		// timeout is the authority and the node count can never leak.
		window.setTimeout(() => {
			p.remove();
			particles -= 1;
		}, fx.life + 60);
	};

	const frame = (now) => {
		raf = 0;
		if (!cfg) {
			return;
		}

		const speed = cfg.speed || 0.2;
		rx += (px - rx) * speed;
		ry += (py - ry) * speed;

		dot.style.transform = `translate3d(${rx}px, ${ry}px, 0)`;

		spawn(now);

		raf = window.requestAnimationFrame(frame);
	};

	const start = () => {
		if (!raf) {
			raf = window.requestAnimationFrame(frame);
		}
	};

	const stop = () => {
		if (raf) {
			window.cancelAnimationFrame(raf);
			raf = 0;
		}
	};

	/**
	 * Paint one state (normal | pointer) onto the shared node.
	 */
	const paint = (name) => {
		if (state === name || !cfg) {
			return;
		}
		state = name;

		const s = cfg[name];
		if (!s || s.type === 'default') {
			dot.classList.remove('is-visible');
			dot.innerHTML = '';
			return;
		}

		dot.style.setProperty('--eap-cc-size', `${s.size}px`);
		dot.style.setProperty('--eap-cc-radius', s.radius || '50%');
		dot.style.setProperty('--eap-cc-color', s.color || 'currentColor');
		dot.style.setProperty('--eap-cc-bg', s.bg || 'transparent');
		dot.style.setProperty('--eap-cc-bw', `${s.bw || 0}px`);
		dot.style.setProperty('--eap-cc-bc', s.bc || 'transparent');
		dot.style.setProperty('--eap-cc-opacity', String(s.opacity === undefined ? 1 : s.opacity));

		if (s.type === 'image') {
			const img = document.createElement('img');
			img.src = s.url;
			img.alt = '';
			dot.innerHTML = '';
			dot.appendChild(img);
		} else {
			// Icon and SVG markup were sanitised server-side (wp_kses with an
			// SVG allowlist; no script, no handlers, no href).
			dot.innerHTML = s.html || '';
		}

		dot.classList.add('is-visible');
	};

	const release = () => {
		if (host) {
			host.classList.remove('eap-cc-hide');
		}
		host = null;
		cfg = null;
		state = '';
		seeded = false;
		if (dot) {
			dot.classList.remove('is-visible');
			dot.innerHTML = '';
		}
		stop();
	};

	const adopt = (el) => {
		let parsed;
		try {
			parsed = JSON.parse(el.getAttribute('data-eap-cc') || '');
		} catch (error) {
			return;
		}
		if (!parsed) {
			return;
		}

		build();

		host = el;
		cfg = parsed;
		state = '';

		root.style.setProperty('--eap-cc-z', String(cfg.z || 99999));
		dot.style.setProperty('--eap-cc-blend', cfg.blend || 'normal');
		if (cfg.trail) {
			root.style.setProperty('--eap-cc-trail-color', cfg.trail.color || '#6366f1');
		}

		if (cfg.hide) {
			el.classList.add('eap-cc-hide');
		}

		start();
	};

	document.addEventListener('mouseover', (event) => {
		const target = event.target;
		if (!target || typeof target.closest !== 'function') {
			return;
		}

		// closest() picks the innermost, so a cursor-enabled card inside a
		// cursor-enabled section hands over cleanly in both directions.
		const el = target.closest('[data-eap-cc]');

		if (el !== host) {
			release();
			if (el) {
				adopt(el);
			}
		}

		if (!cfg) {
			return;
		}

		// Pointer state is decided per event, so moving onto a link inside the
		// host switches without needing its own listener.
		const clickable = cfg.pointerSelector
			&& target.closest(cfg.pointerSelector)
			&& host.contains(target);

		paint(clickable ? 'pointer' : 'normal');
	});

	document.addEventListener('mouseout', (event) => {
		if (!host) {
			return;
		}
		// mouseout also fires crossing into a child; only a relatedTarget outside
		// the host (or none, i.e. leaving the window) is a real exit.
		const to = event.relatedTarget;
		if (to && host.contains(to)) {
			return;
		}
		release();
	});

	document.addEventListener('mousemove', (event) => {
		px = event.clientX;
		py = event.clientY;

		// Without seeding, the cursor eases in from 0,0 across the whole viewport
		// the first time it is shown.
		if (!seeded) {
			seeded = true;
			rx = px;
			ry = py;
		}
	}, { passive: true });

	// A scrolled page moves content under a stationary pointer, so the element
	// beneath it can change with no mouse event at all.
	window.addEventListener('scroll', () => {
		if (!host) {
			return;
		}
		const under = document.elementFromPoint(px, py);
		if (!under || !host.contains(under)) {
			release();
		}
	}, { passive: true });
})();
