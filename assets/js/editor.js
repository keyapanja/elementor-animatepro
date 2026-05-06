(() => {
	const badgeClass = 'eap-widget-badge';
	const readyClass = 'eap-widget-badge-ready';
	const structureReadyClass = 'eap-structure-badge-ready';
	const eapWidgetTitles = new Set([
		'image box',
		'image box slider',
		'icon box',
		'image hotspot',
		'social icons',
		'image',
		'image gallery',
		'image comparison',
		'parallax sections',
		'text hover image',
		'brand slider',
		'testimonial box',
		'testimonial slider',
		'advanced button',
		'animated text',
		'advanced animated text'
	]);
	const searchSafeEapWidgetTitles = new Set([
		'image hotspot',
		'image comparison',
		'parallax sections',
		'text hover image',
		'brand slider',
		'testimonial box',
		'testimonial slider',
		'advanced button',
		'advanced animated text'
	]);

	const normalizeText = (value) => String(value || '')
		.replace(/\s+/g, ' ')
		.trim()
		.toLowerCase();

	const getTitleText = (element) => {
		if (!element) {
			return '';
		}

		const selectors = [
			'.title',
			'.elementor-element-title',
			'.elementor-panel-heading-title',
			'.elementor-navigator__element__title',
			'.elementor-navigator__element__title__text',
			'.elementor-navigator__item__title',
			'.elementor-navigator__title',
			'.elementor-element-title-wrapper'
		];

		for (const selector of selectors) {
			const node = element.querySelector(selector);
			if (node && normalizeText(node.textContent)) {
				return normalizeText(node.textContent);
			}
		}

		return normalizeText(element.textContent);
	};

	const isEapTypeNode = (element) => {
		if (!element) {
			return false;
		}

		const widgetType = normalizeText(element.getAttribute('data-widget_type'));
		const elementType = normalizeText(element.getAttribute('data-element_type'));

		return widgetType.includes('eap-') || elementType.includes('eap-');
	};

	const hasEapWidgetInTree = (element) => {
		if (!element) {
			return false;
		}

		if (isEapTypeNode(element)) {
			return true;
		}

		return Boolean(element.querySelector('[data-widget_type*="eap-"], [data-element_type*="eap-"]'));
	};

	const isInAnimateProCategory = (element) => {
		if (!element) {
			return false;
		}

		const category = element.closest('.elementor-panel-category');
		if (!category) {
			return false;
		}

		const title = category.querySelector('.elementor-panel-category-title, .elementor-panel-heading-title, .title');
		return normalizeText(title?.textContent) === 'animatepro';
	};

	const isKnownEapTitle = (element) => eapWidgetTitles.has(getTitleText(element));
	const isSearchSafeEapTitle = (element) => searchSafeEapWidgetTitles.has(getTitleText(element));

	const removeDirectBadge = (element, mode = 'panel') => {
		if (!element) {
			return;
		}

		const className = mode === 'structure' ? structureReadyClass : readyClass;
		element.classList.remove(className);

		Array.from(element.children).forEach((child) => {
			if (child.classList && child.classList.contains(badgeClass)) {
				child.remove();
			}
		});
	};

	const ensureBadge = (element, mode = 'panel') => {
		if (!element) {
			return;
		}

		const className = mode === 'structure' ? structureReadyClass : readyClass;
		const directBadge = Array.from(element.children).find(
			(child) => child.classList && child.classList.contains(badgeClass)
		);

		element.classList.add(className);

		if (directBadge) {
			return;
		}

		const badge = document.createElement('span');
		badge.className = badgeClass;
		badge.textContent = 'EAP';
		element.appendChild(badge);
	};

	const markPanelWidgets = () => {
		const panelNodes = document.querySelectorAll(
			'.elementor-panel .elementor-element, .elementor-panel .elementor-element-wrapper'
		);

		panelNodes.forEach((element) => {
			if (
				hasEapWidgetInTree(element) ||
				(isInAnimateProCategory(element) && isKnownEapTitle(element)) ||
				isSearchSafeEapTitle(element)
			) {
				ensureBadge(element, 'panel');
				return;
			}

			removeDirectBadge(element, 'panel');
		});
	};

	const markStructureWidgets = () => {
		const structureItems = document.querySelectorAll('.elementor-navigator__item');

		structureItems.forEach((item) => {
			const titleNode = item.querySelector(
				'.elementor-navigator__element__title__text, .elementor-navigator__element__title, .elementor-navigator__item__title, .elementor-navigator__title'
			);
			const element = item.closest('.elementor-navigator__element') || item;

			if (isEapTypeNode(element) || isKnownEapTitle(titleNode || item)) {
				ensureBadge(item, 'structure');
				return;
			}

			removeDirectBadge(item, 'structure');
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
		observeRoot(document.querySelector('#elementor-panel-elements'));
		observeRoot(document.querySelector('.elementor-panel'));
		observeRoot(document.querySelector('.elementor-navigator'));
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
