document.addEventListener('DOMContentLoaded', () => {
	const globalSearch = document.querySelector('[data-eap-global-search]');
	const searchToggle = document.querySelector('[data-eap-search-toggle]');
	const searchWrap = document.querySelector('.eap-topbar__searchwrap');
	const settingsForm = document.querySelector('[data-eap-settings-form]');
	const autoSaveInput = document.querySelector('[data-eap-autosave]');
	const adminNotice = document.querySelector('[data-eap-admin-notice]');
	const noticeDismiss = document.querySelector('[data-eap-notice-dismiss]');
	const tabs = Array.from(document.querySelectorAll('.eap-tab'));
	const groups = Array.from(document.querySelectorAll('.eap-widget-group'));
	const cards = Array.from(document.querySelectorAll('[data-eap-widget-card]'));
	const groupToggles = Array.from(document.querySelectorAll('.eap-group-toggle'));
	let autoSaveTimer;

	const getActiveTab = () => {
		const active = document.querySelector('.eap-tab.is-active');
		return active ? active.dataset.eapTab : 'all';
	};

	const syncWidgetVisibility = () => {
		const query = globalSearch ? globalSearch.value.trim().toLowerCase() : '';
		const activeTab = getActiveTab();

		groups.forEach((group) => {
			const groupKey = group.dataset.eapPanel;
			let visibleCards = 0;

			group.querySelectorAll('[data-eap-widget-card]').forEach((card) => {
				const name = card.dataset.eapWidgetName || '';
				const matchesSearch = !query || name.includes(query);
				const matchesTab = activeTab === 'all' || activeTab === groupKey;
				const visible = matchesSearch && matchesTab;

				card.classList.toggle('is-hidden', !visible);

				if (visible) {
					visibleCards += 1;
				}
			});

			group.classList.toggle('is-hidden', visibleCards === 0);
		});
	};

	tabs.forEach((tab) => {
		tab.addEventListener('click', () => {
			tabs.forEach((item) => item.classList.remove('is-active'));
			tab.classList.add('is-active');
			syncWidgetVisibility();
		});
	});

	if (globalSearch) {
		globalSearch.addEventListener('input', syncWidgetVisibility);
	}

	if (searchToggle && searchWrap && globalSearch) {
		searchToggle.addEventListener('click', () => {
			const isOpen = searchWrap.classList.toggle('is-open');
			searchToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			if (isOpen) {
				window.setTimeout(() => globalSearch.focus(), 120);
			} else if (globalSearch.value) {
				globalSearch.value = '';
				syncWidgetVisibility();
			}
		});
	}

	const updateGroupToggleState = (scope) => {
		const toggle = document.querySelector(`.eap-group-toggle[data-eap-scope="${scope}"]`);
		if (!toggle) {
			return;
		}

		const inputs = cards
			.filter((card) => card.dataset.eapWidgetGroup === scope && card.dataset.eapAvailable !== 'no')
			.map((card) => card.querySelector('[data-eap-toggle-input]'))
			.filter(Boolean);

		if (!inputs.length) {
			return;
		}

		const allEnabled = inputs.every((input) => input.checked);
		toggle.dataset.eapState = allEnabled ? 'enabled' : 'disabled';

		const label = toggle.querySelector('.eap-inline-toggle__label');
		if (label) {
			label.textContent = allEnabled ? 'Disable All' : 'Enable All';
		}
	};

	document.querySelectorAll('.eap-bulk-toggle').forEach((button) => {
		button.addEventListener('click', () => {
			const scope = button.dataset.eapScope || 'all';
			const mode = button.dataset.eapToggle;
			const enabled = mode === 'enable';

			cards.forEach((card) => {
				const input = card.querySelector('[data-eap-toggle-input]');
				if (!input) {
					return;
				}

				if (card.dataset.eapAvailable === 'no') {
					return;
				}

				if (scope !== 'all' && card.dataset.eapWidgetGroup !== scope) {
					return;
				}

				input.checked = enabled;
			});

			groupToggles.forEach((toggle) => updateGroupToggleState(toggle.dataset.eapScope));
			triggerAutoSave();
		});
	});

	groupToggles.forEach((toggle) => {
		const scope = toggle.dataset.eapScope;
		updateGroupToggleState(scope);

		toggle.addEventListener('click', () => {
			const disableGroup = toggle.dataset.eapState === 'enabled';
			cards.forEach((card) => {
				if (card.dataset.eapWidgetGroup !== scope) {
					return;
				}

				if (card.dataset.eapAvailable === 'no') {
					return;
				}

				const input = card.querySelector('[data-eap-toggle-input]');
				if (input) {
					input.checked = !disableGroup;
				}
			});

			updateGroupToggleState(scope);
			triggerAutoSave();
		});
	});

	cards.forEach((card) => {
		const input = card.querySelector('[data-eap-toggle-input]');
		if (!input) {
			return;
		}

		input.addEventListener('change', () => {
			updateGroupToggleState(card.dataset.eapWidgetGroup);
			triggerAutoSave();
		});
	});

	const triggerAutoSave = () => {
		if (!settingsForm || !autoSaveInput || !autoSaveInput.checked) {
			return;
		}

		window.clearTimeout(autoSaveTimer);
		autoSaveTimer = window.setTimeout(() => {
			settingsForm.submit();
		}, 350);
	};

	const clearUpdatedFlag = () => {
		const url = new URL(window.location.href);
		if (!url.searchParams.has('eap-updated')) {
			return;
		}

		url.searchParams.delete('eap-updated');
		window.history.replaceState({}, document.title, url.toString());
	};

	if (adminNotice) {
		clearUpdatedFlag();
	}

	if (noticeDismiss && adminNotice) {
		noticeDismiss.addEventListener('click', () => {
			adminNotice.remove();
		});
	}

	syncWidgetVisibility();
});
