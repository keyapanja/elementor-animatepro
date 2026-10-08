/**
 * Theme Builder admin screen.
 *
 * Row actions post through one hidden form, so every change goes back through
 * admin-post.php with a nonce rather than a bespoke AJAX write. The conditions
 * editor is the only rich part: rule rows are built from the schema the server
 * localised, and the controls that point at entries, terms or authors search
 * over AJAX because a site can have far too many of those to inline.
 */

document.addEventListener('DOMContentLoaded', () => {
	const shell = document.querySelector('.eap-tb');

	if (!shell) {
		return;
	}

	const data = window.EAPThemeBuilder || {};
	const schema = data.schema || {};
	const i18n = data.i18n || {};
	const templates = data.templates || {};
	const typeLabels = data.types || {};

	const taskForm = document.querySelector('[data-eap-tb-task-form]');
	const nameModal = document.querySelector('[data-eap-tb-modal="name"]');
	const conditionsModal = document.querySelector('[data-eap-tb-modal="conditions"]');
	const previewModal = document.querySelector('[data-eap-tb-modal="preview"]');
	const rulesHost = conditionsModal ? conditionsModal.querySelector('[data-eap-tb-rules]') : null;

	let ruleIndex = 0;

	const el = (tag, className) => {
		const node = document.createElement(tag);
		if (className) {
			node.className = className;
		}
		return node;
	};

	const makeSelect = (name, options, selected) => {
		const select = el('select');

		if (name) {
			select.name = name;
		}

		Object.keys(options).forEach((value) => {
			const option = el('option');
			option.value = value;
			option.textContent = options[value];
			if (String(value) === String(selected)) {
				option.selected = true;
			}
			select.appendChild(option);
		});

		return select;
	};

	/* Section tabs ------------------------------------------------------- */

	const tabs = Array.from(document.querySelectorAll('[data-eap-tb-panel]'));
	const panels = Array.from(document.querySelectorAll('[data-eap-tb-panel-body]'));

	const showPanel = (name) => {
		tabs.forEach((tab) => {
			const active = tab.dataset.eapTbPanel === name;
			tab.classList.toggle('is-active', active);
			tab.setAttribute('aria-selected', active ? 'true' : 'false');
		});

		panels.forEach((panel) => {
			panel.hidden = panel.dataset.eapTbPanelBody !== name;
		});
	};

	/* Modals ------------------------------------------------------------- */

	const closeModals = () => {
		[nameModal, conditionsModal, previewModal].forEach((modal) => {
			if (modal) {
				modal.hidden = true;
			}
		});

		document.body.classList.remove('eap-tb-modal-open');
	};

	const openModal = (modal) => {
		if (!modal) {
			return;
		}
		closeModals();
		modal.hidden = false;
		modal.scrollTop = 0;
		document.body.classList.add('eap-tb-modal-open');
	};

	/* Row actions -------------------------------------------------------- */

	const runTask = (task, id, enabled) => {
		if (!taskForm) {
			return;
		}

		taskForm.querySelector('[name="eap_tb_task"]').value = task;
		taskForm.querySelector('[name="template_id"]').value = id || '';
		taskForm.querySelector('[name="enabled"]').value = typeof enabled === 'undefined' ? '' : enabled;
		taskForm.submit();
	};

	/* Name modal --------------------------------------------------------- */

	const openNameModal = (options) => {
		if (!nameModal) {
			return;
		}

		const input = nameModal.querySelector('[data-eap-tb-name-input]');
		const title = nameModal.querySelector('[data-eap-tb-name-title]');
		const submit = nameModal.querySelector('[data-eap-tb-name-submit]');

		nameModal.querySelector('[data-eap-tb-name-task]').value = options.task;
		nameModal.querySelector('[data-eap-tb-name-type]').value = options.type || '';
		nameModal.querySelector('[data-eap-tb-name-id]').value = options.id || '';

		input.value = options.value || '';
		title.textContent = options.title || '';
		submit.textContent = options.cta || '';

		openModal(nameModal);
		input.focus();
		input.select();
	};

	const openCreate = (type) => {
		const label = typeLabels[type] || type;

		openNameModal({
			task: 'create',
			type,
			value: label,
			title: (i18n.newTitle || '%s').replace('%s', label),
			cta: i18n.createCta || '',
		});
	};

	const openRename = (id, currentName) => {
		openNameModal({
			task: 'rename',
			id,
			value: currentName || '',
			title: i18n.renameTitle || '',
			cta: i18n.saveCta || '',
		});
	};

	/* Conditions --------------------------------------------------------- */

	const lookup = (kind, query) => {
		const url = new URL(data.ajaxUrl, window.location.origin);

		url.searchParams.set('action', 'eap_tb_search');
		url.searchParams.set('nonce', data.nonce || '');
		url.searchParams.set('kind', kind);
		url.searchParams.set('q', query || '');

		return fetch(url.toString(), { credentials: 'same-origin' })
			.then((response) => response.json())
			.then((payload) => (payload && payload.success && Array.isArray(payload.data) ? payload.data : []))
			.catch(() => []);
	};

	const syncEmptyState = () => {
		if (!rulesHost) {
			return;
		}

		const existing = rulesHost.querySelector('.eap-tb-rules__empty');
		const hasRules = rulesHost.querySelector('.eap-tb-rule');

		if (hasRules) {
			if (existing) {
				existing.remove();
			}
			return;
		}

		if (!existing) {
			const empty = el('p', 'eap-tb-rules__empty');
			empty.textContent = i18n.emptyRules || '';
			rulesHost.appendChild(empty);
		}
	};

	const buildValueControl = (host, fieldName, kind, value, label) => {
		host.innerHTML = '';
		host.classList.remove('eap-tb-rule__value--search');

		if (!kind || kind === 'none') {
			host.hidden = true;
			return;
		}

		host.hidden = false;

		if (kind === 'text') {
			const input = el('input');
			input.type = 'text';
			input.name = fieldName;
			input.value = value || '';
			input.placeholder = i18n.searchTerm || '';
			host.appendChild(input);
			return;
		}

		if (kind === 'post_type' || kind === 'taxonomy') {
			const options = kind === 'post_type' ? data.postTypes || {} : data.taxonomies || {};
			host.appendChild(makeSelect(fieldName, options, value));
			return;
		}

		// Entries, terms and authors carry names long enough to need the width
		// of the whole row, so this pair drops onto its own line.
		host.classList.add('eap-tb-rule__value--search');

		const search = el('input');
		search.type = 'search';
		search.placeholder = i18n.search || '';

		const picker = makeSelect(fieldName, {}, '');

		if (value) {
			const option = el('option');
			option.value = value;
			option.textContent = label || value;
			option.selected = true;
			picker.appendChild(option);
		}

		host.appendChild(search);
		host.appendChild(picker);

		const load = (query) => {
			const keep = picker.value;
			const keepOption = picker.options[picker.selectedIndex];
			const keepLabel = keepOption ? keepOption.textContent : '';

			lookup(kind, query).then((items) => {
				picker.innerHTML = '';
				let matched = false;

				items.forEach((item) => {
					const option = el('option');
					option.value = item.value;
					option.textContent = item.label;
					if (String(item.value) === String(keep)) {
						option.selected = true;
						matched = true;
					}
					picker.appendChild(option);
				});

				// Keep whatever was already chosen reachable even when it falls
				// outside the current search results.
				if (keep && !matched) {
					const option = el('option');
					option.value = keep;
					option.textContent = keepLabel || keep;
					option.selected = true;
					picker.insertBefore(option, picker.firstChild);
				}
			});
		};

		let timer;
		search.addEventListener('input', () => {
			window.clearTimeout(timer);
			timer = window.setTimeout(() => load(search.value.trim()), 250);
		});

		load('');
	};

	const createRuleRow = (rule) => {
		const current = rule || {};
		const index = ruleIndex;
		ruleIndex += 1;

		const row = el('div', 'eap-tb-rule');

		const mode = makeSelect(
			`rules[${index}][mode]`,
			{ include: i18n.include || 'Show on', exclude: i18n.exclude || 'Hide on' },
			current.mode || 'include'
		);

		const scopeOptions = {};
		Object.keys(schema).forEach((key) => {
			scopeOptions[key] = schema[key].label;
		});

		const scope = makeSelect(`rules[${index}][scope]`, scopeOptions, current.scope || 'entire');

		const sub = el('select');
		sub.name = `rules[${index}][sub]`;

		const valueHost = el('div', 'eap-tb-rule__value');

		const remove = el('button', 'eap-tb-rule__remove');
		remove.type = 'button';
		remove.textContent = '×';
		remove.title = i18n.remove || '';
		remove.setAttribute('aria-label', i18n.remove || '');

		const fillSubs = (selected) => {
			const subs = (schema[scope.value] && schema[scope.value].subs) || {};
			sub.innerHTML = '';

			Object.keys(subs).forEach((key) => {
				const option = el('option');
				option.value = key;
				option.textContent = subs[key].label;
				if (key === selected) {
					option.selected = true;
				}
				sub.appendChild(option);
			});
		};

		const fillValue = (value, label) => {
			const subs = (schema[scope.value] && schema[scope.value].subs) || {};
			const definition = subs[sub.value];
			buildValueControl(valueHost, `rules[${index}][value]`, definition ? definition.value : 'none', value, label);
		};

		scope.addEventListener('change', () => {
			fillSubs('');
			fillValue('', '');
		});

		sub.addEventListener('change', () => fillValue('', ''));

		remove.addEventListener('click', () => {
			row.remove();
			syncEmptyState();
		});

		fillSubs(current.sub || '');
		fillValue(current.value || '', current.label || '');

		row.appendChild(mode);
		row.appendChild(scope);
		row.appendChild(sub);
		row.appendChild(valueHost);
		row.appendChild(remove);

		return row;
	};

	const openConditions = (id) => {
		if (!conditionsModal || !rulesHost) {
			return;
		}

		const template = templates[String(id)];
		const nameNode = conditionsModal.querySelector('[data-eap-tb-conditions-name]');

		conditionsModal.querySelector('[data-eap-tb-conditions-id]').value = id;

		if (nameNode) {
			nameNode.textContent = template ? template.name : '';
		}

		rulesHost.innerHTML = '';
		ruleIndex = 0;

		const rules = template && Array.isArray(template.rules) ? template.rules : [];
		rules.forEach((rule) => rulesHost.appendChild(createRuleRow(rule)));

		syncEmptyState();
		openModal(conditionsModal);
	};

	/* Preview settings --------------------------------------------------- */

	const openPreview = (id) => {
		if (!previewModal) {
			return;
		}

		const template = templates[String(id)] || {};
		const kinds = (data.previews || {})[template.type] || {};
		const current = template.preview || {};
		const kindSelect = previewModal.querySelector('[data-eap-tb-preview-kind]');
		const valueHost = previewModal.querySelector('[data-eap-tb-preview-value]');
		const nameNode = previewModal.querySelector('[data-eap-tb-preview-name]');

		previewModal.querySelector('[data-eap-tb-preview-id]').value = id;

		if (nameNode) {
			nameNode.textContent = template.name || '';
		}

		const options = {};
		Object.keys(kinds).forEach((key) => {
			options[key] = kinds[key].label;
		});

		kindSelect.innerHTML = '';
		Object.keys(options).forEach((key) => {
			const option = el('option');
			option.value = key;
			option.textContent = options[key];
			if (key === current.kind) {
				option.selected = true;
			}
			kindSelect.appendChild(option);
		});

		const sync = (value, label) => {
			const kind = kinds[kindSelect.value];
			buildValueControl(valueHost, 'preview_value', kind ? kind.value : 'none', value, label);
		};

		kindSelect.onchange = () => sync('', '');
		sync(current.value || '', current.label || '');

		openModal(previewModal);
	};

	/* Wiring ------------------------------------------------------------- */

	document.addEventListener('click', (event) => {
		const target = event.target;

		if (!(target instanceof Element)) {
			return;
		}

		const tab = target.closest('[data-eap-tb-panel]');
		if (tab) {
			event.preventDefault();
			showPanel(tab.dataset.eapTbPanel);
			return;
		}

		const close = target.closest('[data-eap-tb-close]');
		if (close) {
			event.preventDefault();
			closeModals();
			return;
		}

		if (target.matches('[data-eap-tb-modal]')) {
			closeModals();
			return;
		}

		const addRule = target.closest('[data-eap-tb-add-rule]');
		if (addRule && rulesHost) {
			event.preventDefault();
			rulesHost.appendChild(createRuleRow(null));
			syncEmptyState();
			return;
		}

		const create = target.closest('[data-eap-tb-create]');
		if (create) {
			event.preventDefault();
			openCreate(create.dataset.eapTbCreate);
			return;
		}

		const conditions = target.closest('[data-eap-tb-conditions]');
		if (conditions) {
			event.preventDefault();
			openConditions(conditions.dataset.eapTbConditions);
			return;
		}

		const preview = target.closest('[data-eap-tb-preview]');
		if (preview) {
			event.preventDefault();
			openPreview(preview.dataset.eapTbPreview);
			return;
		}

		const rename = target.closest('[data-eap-tb-rename]');
		if (rename) {
			event.preventDefault();
			openRename(rename.dataset.eapTbRename, rename.dataset.eapTbName);
			return;
		}

		const task = target.closest('button[data-eap-tb-task]');
		if (task) {
			event.preventDefault();

			if (task.dataset.eapTbConfirm && !window.confirm(i18n.deleteCheck || '')) {
				return;
			}

			runTask(task.dataset.eapTbTask, task.dataset.eapTbId);
		}
	});

	document.addEventListener('change', (event) => {
		const input = event.target;

		if (input instanceof Element && input.matches('input[data-eap-tb-task="toggle"]')) {
			runTask('toggle', input.dataset.eapTbId, input.checked ? '1' : '0');
		}
	});

	document.addEventListener('keydown', (event) => {
		if ('Escape' === event.key) {
			closeModals();
		}
	});

	/* Search ------------------------------------------------------------- */

	const globalSearch = document.querySelector('[data-eap-global-search]');

	if (globalSearch) {
		const groups = Array.from(document.querySelectorAll('.eap-tb-group'));
		const noMatch = document.querySelector('[data-eap-tb-no-match]');

		globalSearch.addEventListener('input', () => {
			const query = globalSearch.value.trim().toLowerCase();
			let found = 0;

			// Templates are the only thing worth searching, so searching takes
			// you to them rather than filtering a panel you cannot see.
			if (query) {
				showPanel('templates');
			}

			groups.forEach((group) => {
				let visible = 0;

				group.querySelectorAll('.eap-tb-row').forEach((row) => {
					const nameNode = row.querySelector('.eap-tb-row__name');
					const name = nameNode ? nameNode.textContent.trim().toLowerCase() : '';
					const match = !query || name.indexOf(query) !== -1;

					row.hidden = !match;

					if (match) {
						visible += 1;
					}
				});

				group.hidden = '' !== query && 0 === visible;
				found += visible;
			});

			if (noMatch) {
				noMatch.hidden = '' === query || found > 0;
			}
		});
	}
});
