(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	// Containers are mapped by ORDER (not data attributes) so this works in the
	// editor too, where the freshly-dropped child containers don't yet carry the
	// frontend data-tab-index attribute.
	const getContainers = (content) => {
		if (!content) {
			return [];
		}
		const scoped = content.querySelectorAll(':scope > .e-con');
		if (scoped.length) {
			return Array.from(scoped);
		}
		return Array.from(content.children).filter((node) => node.nodeType === 1);
	};

	const setup = (el, editorMode) => {
		if (el.dataset.eapCtBound === 'true') {
			return;
		}
		el.dataset.eapCtBound = 'true';

		const switcher = el.querySelector('.eap-content-toggle__switcher');
		const content = el.querySelector('.eap-content-toggle__content');
		if (!switcher) {
			return;
		}

		const labels = Array.from(switcher.querySelectorAll('.eap-content-toggle__label'));
		if (!labels.length) {
			return;
		}

		let activeIndex = 0;

		// Apply the active state to the labels + the panes. Containers are
		// re-queried every call so it stays correct as the editor injects or
		// replaces the nested panes. Adding `eap-ct-pane` here (not only in PHP
		// render) means the CSS hides inactive panes in the EDITOR too, so the
		// builder shows one condition at a time — exactly like the front end.
		const applyActive = (rawIndex) => {
			activeIndex = Math.max(0, Math.min(labels.length - 1, rawIndex));
			switcher.style.setProperty('--eap-ct-index', activeIndex);

			labels.forEach((label, i) => {
				const active = i === activeIndex;
				label.setAttribute('aria-selected', active ? 'true' : 'false');
				label.setAttribute('tabindex', active ? '0' : '-1');
			});

			getContainers(content).forEach((container, i) => {
				container.classList.add('eap-ct-pane');
				container.classList.toggle('e-active', i === activeIndex);
			});
		};

		labels.forEach((label, i) => {
			label.addEventListener('click', (event) => {
				event.preventDefault();
				applyActive(i);
			});

			label.addEventListener('keydown', (event) => {
				if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
					event.preventDefault();
					const next = (i + 1) % labels.length;
					labels[next].focus();
					applyActive(next);
				} else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
					event.preventDefault();
					const prev = (i - 1 + labels.length) % labels.length;
					labels[prev].focus();
					applyActive(prev);
				}
			});
		});

		let initial = parseInt(el.dataset.defaultIndex || '0', 10);
		if (isNaN(initial)) {
			initial = 0;
		}
		applyActive(initial);

		// In the editor the nested panes can be injected / added / removed after
		// this runs; re-apply so a newly mounted container is tagged and only the
		// active pane stays visible while building.
		if (editorMode && content && 'MutationObserver' in window) {
			const observer = new MutationObserver(() => applyActive(activeIndex));
			observer.observe(content, { childList: true });
		}
	};

	// ----------------------------------------------------------------------
	// Deep linking (front end): when the URL hash matches a condition's Custom
	// ID, open that condition and scroll its toggle into view.
	// ----------------------------------------------------------------------
	const activateFromHash = () => {
		const raw = (window.location.hash || '').replace(/^#/, '');
		if (!raw) {
			return;
		}

		const safe = (window.CSS && typeof CSS.escape === 'function')
			? CSS.escape(raw)
			: raw.replace(/[^A-Za-z0-9_-]/g, '');
		if (!safe) {
			return;
		}

		const button = document.querySelector('.eap-content-toggle__label[data-eap-ct-cid="' + safe + '"]');
		if (!button) {
			return;
		}

		button.click();

		const widget = button.closest('.eap-content-toggle');
		if (widget) {
			window.requestAnimationFrame(() => {
				widget.scrollIntoView({ behavior: 'smooth', block: 'start' });
			});
		}
	};

	let hashBound = false;
	const bindHash = () => {
		if (hashBound) {
			return;
		}
		hashBound = true;
		window.addEventListener('hashchange', activateFromHash);
		if (window.location.hash) {
			// Defer so the toggles on the page have finished binding first.
			window.setTimeout(activateFromHash, 60);
		}
	};

	api.register('content-toggle', (root, { editorMode = false } = {}) => {
		api.getNodes(root, '.eap-content-toggle').forEach((el) => setup(el, editorMode));
		if (!editorMode) {
			bindHash();
		}
	});
})();
