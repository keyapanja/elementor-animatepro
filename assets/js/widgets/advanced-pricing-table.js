/*
 * Advanced Pricing Table — the billing toggle.
 *
 * Both prices are already in the DOM as data attributes, so switching is a text
 * swap with no request and nothing to reflow beyond the price line.
 */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-apt]';

	const setup = (root) => {
		if (root.dataset.eapAptBound === '1') {
			return;
		}
		root.dataset.eapAptBound = '1';

		const pills = Array.from(root.querySelectorAll('.eap-apt__billing-pill'));
		if (!pills.length) {
			return;
		}

		// Both the amount and the period swap, so a yearly price can carry its own
		// "/year" without the author repeating themselves.
		const fields = Array.from(root.querySelectorAll('.eap-apt__amount, .eap-apt__period'));

		const apply = (mode) => {
			fields.forEach((field) => {
				const value = field.getAttribute(mode === 'alt' ? 'data-alt' : 'data-primary');
				if (value !== null) {
					field.textContent = value;
				}
			});

			pills.forEach((pill) => {
				const on = pill.getAttribute('data-billing') === mode;
				pill.classList.toggle('is-active', on);
				pill.setAttribute('aria-pressed', on ? 'true' : 'false');
			});
		};

		pills.forEach((pill) => {
			pill.addEventListener('click', () => apply(pill.getAttribute('data-billing')));
		});
	};

	const run = (root) => {
		const nodes = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((node) => setup(node));
	};

	if (api && typeof api.register === 'function') {
		api.register('advanced-pricing-table', (root) => run(root));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
