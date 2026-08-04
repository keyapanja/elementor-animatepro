(() => {
	const badgeClass = 'eap-widget-badge';
	const panelReadyClass = 'eap-widget-badge-ready';
	const structureReadyClass = 'eap-structure-badge-ready';

	// Fallback list of this plugin's widget titles, used only for the structure
	// (navigator) panel, which exposes titles but not widget types. The panel /
	// search list does NOT rely on this — it matches the real widget type via
	// data-library-element-type, so it can never tag a same-named Elementor
	// default widget.
	const fallbackEapTitles = new Set([
		'image box',
		'image box slider',
		'icon box',
		'image hotspot',
		'social icons',
		'image',
		'image gallery',
		'image comparison',
		'progress bar',
		'team',
		'parallax sections',
		'text hover image',
		'brand slider',
		'testimonial box',
		'testimonial slider',
		'advanced button',
		'animated text',
		'advanced animated text',
		'timeline',
		'services tabs',
		'one page nav',
		'advanced testimonial slider',
		'advanced slider'
	]);

	const normalizeText = (value) => String(value || '')
		.replace(/\s+/g, ' ')
		.trim()
		.toLowerCase();

	// Titles of registered eap-* widgets, read from Elementor's widget cache so
	// the structure panel stays correct even if the widget set changes. Merged
	// with the fallback above. Cached once Elementor has populated the data.
	let eapTitleCache = null;
	const getEapTitles = () => {
		if (eapTitleCache) {
			return eapTitleCache;
		}

		const titles = new Set(fallbackEapTitles);
		const cache = window.elementor
			&& (window.elementor.widgetsCache
				|| (window.elementor.config && window.elementor.config.widgets));

		if (cache && typeof cache === 'object') {
			Object.keys(cache).forEach((name) => {
				if (name.indexOf('eap-') !== 0) {
					return;
				}
				const widget = cache[name] || {};
				if (widget.title) {
					titles.add(normalizeText(widget.title));
				}
			});
			eapTitleCache = titles;
		}

		return titles;
	};

	const isEapPanelTile = (element) => {
		if (!element || !element.getAttribute) {
			return false;
		}
		const type = normalizeText(element.getAttribute('data-library-element-type'));
		return type.indexOf('eap-') === 0;
	};

	const ensureBadge = (element, readyClass) => {
		if (!element) {
			return;
		}

		element.classList.add(readyClass);

		const existing = Array.from(element.children).find(
			(child) => child.classList && child.classList.contains(badgeClass)
		);
		if (existing) {
			return;
		}

		const badge = document.createElement('span');
		badge.className = badgeClass;
		badge.textContent = 'EAP';
		element.appendChild(badge);
	};

	const removeBadge = (element, readyClass) => {
		if (!element) {
			return;
		}

		element.classList.remove(readyClass);

		Array.from(element.children).forEach((child) => {
			if (child.classList && child.classList.contains(badgeClass)) {
				child.remove();
			}
		});
	};

	// Widget list + search results share the same tile template:
	//   <button class="elementor-element" data-library-element-type="eap-...">
	// so this single pass covers both the normal category view and search.
	const markPanelWidgets = () => {
		document.querySelectorAll('.elementor-element[data-library-element-type]').forEach((element) => {
			if (isEapPanelTile(element)) {
				ensureBadge(element, panelReadyClass);
			} else {
				removeBadge(element, panelReadyClass);
			}
		});
	};

	const markStructureWidgets = () => {
		const titles = getEapTitles();

		document.querySelectorAll('.elementor-navigator__item').forEach((item) => {
			const titleNode = item.querySelector(
				'.elementor-navigator__element__title__text, .elementor-navigator__element__title'
			);
			const title = normalizeText((titleNode || item).textContent);

			if (title && titles.has(title)) {
				ensureBadge(item, structureReadyClass);
			} else {
				removeBadge(item, structureReadyClass);
			}
		});
	};

	const markWidgets = () => {
		markPanelWidgets();
		markStructureWidgets();
	};

	let frameId = null;
	const scheduleMarkWidgets = () => {
		if (frameId) {
			return;
		}

		frameId = window.requestAnimationFrame(() => {
			frameId = null;
			markWidgets();
		});
	};

	const observeRoot = (root) => {
		if (!root) {
			return;
		}

		const observer = new MutationObserver(() => {
			scheduleMarkWidgets();
		});

		observer.observe(root, {
			childList: true,
			subtree: true
		});
	};

	const init = () => {
		scheduleMarkWidgets();
		observeRoot(document.body);
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
