(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	// Loose numeric parse so "$1,200", "45%", "3.5" sort as numbers.
	const parseNum = (str) => parseFloat(String(str).replace(/[^0-9.\-]/g, ''));

	// Page numbers to show, collapsing long ranges with an ellipsis marker.
	const pageWindow = (current, total) => {
		const win = [];
		if (total <= 7) {
			for (let i = 1; i <= total; i++) {
				win.push(i);
			}
			return win;
		}
		win.push(1);
		if (current > 4) {
			win.push('…');
		}
		const start = Math.max(2, current - 1);
		const end = Math.min(total - 1, current + 1);
		for (let i = start; i <= end; i++) {
			win.push(i);
		}
		if (current < total - 3) {
			win.push('…');
		}
		win.push(total);
		return win;
	};

	const setup = (el) => {
		if (el.dataset.eapDtBound === 'true') {
			return;
		}
		el.dataset.eapDtBound = 'true';

		const table = el.querySelector('.eap-data-table__table');
		const tbody = table ? table.querySelector('.eap-data-table__tbody') : null;
		if (!tbody) {
			return;
		}

		const rows = Array.from(tbody.querySelectorAll(':scope > tr'));
		rows.forEach((row) => {
			row._eapText = row.textContent.toLowerCase();
		});

		const searchInput = el.querySelector('[data-eap-dt-search]');
		const pager = el.querySelector('[data-eap-dt-pagination]');
		const emptyMsg = el.querySelector('[data-eap-dt-empty-msg]');
		const pageSize = parseInt(el.dataset.eapDtPageSize || '0', 10) || 0;
		const sortable = el.dataset.eapDtSort === '1';

		const state = { query: '', page: 1, sortCol: -1, sortDir: 1 };

		const getMatching = () => {
			if (!state.query) {
				return rows;
			}
			return rows.filter((row) => row._eapText.indexOf(state.query) !== -1);
		};

		const buildPager = (pageCount) => {
			if (!pager) {
				return;
			}
			pager.innerHTML = '';
			if (!pageSize || pageCount <= 1) {
				return;
			}

			const makeButton = (label, page, opts = {}) => {
				const button = document.createElement('button');
				button.type = 'button';
				button.className = 'eap-data-table__page'
					+ (opts.active ? ' is-active' : '')
					+ (opts.ellipsis ? ' eap-data-table__page--ellipsis' : '');
				button.textContent = label;
				if (opts.ellipsis || opts.disabled) {
					button.disabled = true;
				}
				if (!opts.ellipsis && !opts.disabled) {
					button.addEventListener('click', () => {
						state.page = page;
						render();
					});
				}
				return button;
			};

			pager.appendChild(makeButton('‹', state.page - 1, { disabled: state.page <= 1 }));
			pageWindow(state.page, pageCount).forEach((entry) => {
				if (entry === '…') {
					pager.appendChild(makeButton('…', 0, { ellipsis: true }));
				} else {
					pager.appendChild(makeButton(String(entry), entry, { active: entry === state.page }));
				}
			});
			pager.appendChild(makeButton('›', state.page + 1, { disabled: state.page >= pageCount }));
		};

		const render = () => {
			const matching = getMatching();
			const pageCount = pageSize ? Math.max(1, Math.ceil(matching.length / pageSize)) : 1;

			if (state.page > pageCount) {
				state.page = pageCount;
			}
			if (state.page < 1) {
				state.page = 1;
			}

			const start = pageSize ? (state.page - 1) * pageSize : 0;
			const end = pageSize ? start + pageSize : matching.length;
			const visible = new Set(matching.slice(start, end));

			rows.forEach((row) => {
				row.style.display = visible.has(row) ? '' : 'none';
			});

			if (emptyMsg) {
				emptyMsg.hidden = matching.length !== 0;
			}

			buildPager(pageCount);
		};

		// Sorting.
		if (sortable) {
			const ths = Array.from(el.querySelectorAll('.eap-data-table__th[data-eap-dt-th]'));

			const doSort = (col) => {
				if (state.sortCol === col) {
					state.sortDir *= -1;
				} else {
					state.sortCol = col;
					state.sortDir = 1;
				}

				const getCell = (row) => {
					const td = row.children[col];
					return td ? td.textContent.trim() : '';
				};

				const numeric = rows.every((row) => {
					const value = getCell(row);
					return value === '' || !isNaN(parseNum(value));
				});

				rows.sort((a, b) => {
					const av = getCell(a);
					const bv = getCell(b);
					const cmp = numeric
						? ((parseNum(av) || 0) - (parseNum(bv) || 0))
						: av.localeCompare(bv, undefined, { numeric: true, sensitivity: 'base' });
					return cmp * state.sortDir;
				});

				rows.forEach((row) => tbody.appendChild(row));

				ths.forEach((th) => {
					const thCol = parseInt(th.dataset.col, 10);
					th.setAttribute('aria-sort', thCol === col
						? (state.sortDir === 1 ? 'ascending' : 'descending')
						: 'none');
				});

				state.page = 1;
				render();
			};

			ths.forEach((th) => {
				const col = parseInt(th.dataset.col, 10);
				th.addEventListener('click', () => doSort(col));
				th.addEventListener('keydown', (event) => {
					if (event.key === 'Enter' || event.key === ' ') {
						event.preventDefault();
						doSort(col);
					}
				});
			});
		}

		// Search / filter.
		if (searchInput) {
			searchInput.addEventListener('input', () => {
				state.query = searchInput.value.trim().toLowerCase();
				state.page = 1;
				render();
			});
		}

		// Clickable rows.
		tbody.querySelectorAll('[data-eap-dt-href]').forEach((row) => {
			const go = (event) => {
				if (event.target.closest('a, button, input, select, textarea')) {
					return;
				}
				const href = row.dataset.eapDtHref;
				if (!href) {
					return;
				}
				if (row.dataset.eapDtTarget === '_blank') {
					window.open(href, '_blank', 'noopener');
				} else {
					window.location.href = href;
				}
			};
			row.addEventListener('click', go);
			row.addEventListener('keydown', (event) => {
				if (event.key === 'Enter') {
					event.preventDefault();
					go(event);
				}
			});
		});

		// Apply the initial view when search/pagination is active.
		if (pageSize || searchInput) {
			render();
		}
	};

	api.register('data-table', (root) => {
		api.getNodes(root, '[data-eap-data-table]').forEach(setup);
	});
})();
