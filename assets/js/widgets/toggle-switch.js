/*
 * Toggle Switch — a standalone control that drives other elements on the page.
 */
(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '[data-eap-toggle-switch]';

	const read = (key) => {
		try {
			return window.localStorage.getItem(key);
		} catch (error) {
			// Private mode / blocked storage: fall back to the authored default.
			return null;
		}
	};

	const write = (key, value) => {
		try {
			window.localStorage.setItem(key, value);
		} catch (error) {
			// Nothing to do — the switch still works for this page view.
		}
	};

	const nodes = (selector) => {
		if (!selector) {
			return [];
		}
		try {
			return Array.from(document.querySelectorAll(selector));
		} catch (error) {
			// An invalid selector is an authoring mistake, not a reason to throw on
			// every click.
			return [];
		}
	};

	const classes = (value) => String(value || '').split(/\s+/).filter(Boolean);

	const setup = (root) => {
		if (root.dataset.eapTsBound === '1') {
			return;
		}
		root.dataset.eapTsBound = '1';

		let cfg;
		try {
			cfg = JSON.parse(root.getAttribute('data-eap-toggle-switch') || '');
		} catch (error) {
			return;
		}
		if (!cfg) {
			return;
		}

		const control = root.querySelector('.eap-toggle-switch__control');
		const pills = Array.from(root.querySelectorAll('.eap-toggle-switch__pill'));
		const labels = Array.from(root.querySelectorAll('.eap-toggle-switch__label'));

		let on = !!cfg.on;

		if (cfg.remember && cfg.key) {
			const stored = read(cfg.key);
			if (stored === 'on' || stored === 'off') {
				on = stored === 'on';
			}
		}

		const paintTargets = () => {
			if (cfg.action === 'visibility') {
				nodes(cfg.targetOff).forEach((el) => {
					el.classList.toggle('eap-ts-hidden', on);
					if (cfg.animate) { el.classList.add('eap-ts-animated'); }
				});
				nodes(cfg.targetOn).forEach((el) => {
					el.classList.toggle('eap-ts-hidden', !on);
					if (cfg.animate) { el.classList.add('eap-ts-animated'); }
				});
				return;
			}

			if (cfg.action === 'dark') {
				const list = classes(cfg.darkClass);
				if (list.length) {
					document.documentElement.classList.toggle(list[0], on);
					list.slice(1).forEach((c) => document.documentElement.classList.toggle(c, on));
				}
				return;
			}

			// Toggle a class on whatever the selector matches.
			const list = classes(cfg.className);
			if (!list.length) {
				return;
			}
			nodes(cfg.selector).forEach((el) => {
				list.forEach((c) => el.classList.toggle(c, on));
			});
		};

		const paintSelf = () => {
			if (control) {
				control.setAttribute('aria-checked', on ? 'true' : 'false');
			}
			root.classList.toggle('is-on', on);

			labels.forEach((label) => {
				const isOn = label.classList.contains('eap-toggle-switch__label--on');
				label.classList.toggle('is-active', isOn === on);
			});

			pills.forEach((pill) => {
				const isOn = pill.getAttribute('data-state') === 'on';
				pill.classList.toggle('is-active', isOn === on);
				pill.setAttribute('aria-pressed', isOn === on ? 'true' : 'false');
			});
		};

		const apply = (next, persist) => {
			on = next;
			paintSelf();
			paintTargets();

			if (persist && cfg.remember && cfg.key) {
				write(cfg.key, on ? 'on' : 'off');

				/*
				 * Tell sibling switches sharing this key. The native `storage` event
				 * only fires in OTHER tabs, so same-page switches would otherwise
				 * drift out of step.
				 */
				window.dispatchEvent(new CustomEvent('eap-toggle-switch', {
					detail: { key: cfg.key, on }
				}));
			}
		};

		if (control) {
			control.addEventListener('click', () => apply(!on, true));
		}

		pills.forEach((pill) => {
			pill.addEventListener('click', () => apply(pill.getAttribute('data-state') === 'on', true));
		});

		// Clicking a label is the expected way to operate a switch.
		labels.forEach((label) => {
			label.addEventListener('click', () => {
				apply(label.classList.contains('eap-toggle-switch__label--on'), true);
			});
		});

		if (cfg.remember && cfg.key) {
			window.addEventListener('eap-toggle-switch', (event) => {
				if (event.detail && event.detail.key === cfg.key && event.detail.on !== on) {
					apply(event.detail.on, false);
				}
			});

			window.addEventListener('storage', (event) => {
				if (event.key === cfg.key && (event.newValue === 'on' || event.newValue === 'off')) {
					apply(event.newValue === 'on', false);
				}
			});
		}

		apply(on, false);
	};

	const run = (root) => {
		const found = api && typeof api.getNodes === 'function'
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		found.forEach((node) => setup(node));
	};

	if (api && typeof api.register === 'function') {
		api.register('toggle-switch', (root) => run(root));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document));
	} else {
		run(document);
	}
})();
