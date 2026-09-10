(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-tooltip]';

	const POSITIONS = ['top', 'bottom', 'left', 'right'];
	const ANIMATIONS = ['shift-away', 'shift-toward', 'scale', 'fade', 'perspective'];

	const build = (host) => {
		if (host.dataset.eapTtBound === '1') {
			return;
		}
		host.dataset.eapTtBound = '1';

		// getAttribute decodes the entities PHP escaped in, so this is the real
		// markup the server rendered (text / icon / image / shortcode output).
		const content = (host.getAttribute('data-eap-tooltip') || '').trim();
		if (!content) {
			return;
		}

		// Markup that renders nothing visible (an empty wrapper, a shortcode that
		// output nothing) would otherwise show as an empty bubble.
		const probe = document.createElement('div');
		probe.innerHTML = content;
		const hasVisible = probe.textContent.trim() !== '' || probe.querySelector('img, svg, i, picture, video, iframe');
		if (!hasVisible) {
			return;
		}

		const rawPosition = host.getAttribute('data-eap-tt-position') || 'top';
		const position = POSITIONS.indexOf(rawPosition) !== -1 ? rawPosition : 'top';
		const trigger = host.getAttribute('data-eap-tt-trigger') === 'click' ? 'click' : 'hover';
		const arrow = host.getAttribute('data-eap-tt-arrow') === '1';
		const arrowType = host.getAttribute('data-eap-tt-arrow-type') === 'round' ? 'round' : 'sharp';

		const rawAnim = host.getAttribute('data-eap-tt-anim') || 'shift-away';
		const anim = ANIMATIONS.indexOf(rawAnim) !== -1 ? rawAnim : 'shift-away';

		host.classList.add('eap-tooltip-host');

		const classes = ['eap-tooltip', 'eap-tooltip--' + position, 'eap-tooltip--' + anim];
		if (arrow) {
			classes.push('eap-tooltip--arrow', 'eap-tooltip--arrow-' + arrowType);
		}

		const tip = document.createElement('span');
		tip.className = classes.join(' ');
		tip.setAttribute('role', 'tooltip');
		tip.innerHTML = content;
		host.appendChild(tip);

		if (trigger !== 'click') {
			return;
		}

		host.classList.add('eap-tooltip-host--click');

		host.addEventListener('click', (event) => {
			// Let links/buttons inside the tooltip keep working.
			if (tip.contains(event.target)) {
				return;
			}
			event.preventDefault();
			tip.classList.toggle('is-open');
		});

		document.addEventListener('click', (event) => {
			if (!host.contains(event.target)) {
				tip.classList.remove('is-open');
			}
		});

		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape') {
				tip.classList.remove('is-open');
			}
		});
	};

	const run = (root) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((node) => build(node));
	};

	if (api && typeof api.register === 'function') {
		api.register('advanced-tooltip', (root) => run(root));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
