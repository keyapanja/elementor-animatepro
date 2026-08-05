(() => {
	const api = window.EAPFrontend;
	const SELECTOR = '.eap-social-share';

	// Open a share URL in a centered popup; fall back to a new tab if blocked.
	const openPopup = (href) => {
		const w = 600;
		const h = 540;
		const dualLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
		const dualTop = window.screenTop !== undefined ? window.screenTop : window.screenY;
		const width = window.innerWidth || document.documentElement.clientWidth || screen.width;
		const height = window.innerHeight || document.documentElement.clientHeight || screen.height;
		const left = Math.round(width / 2 - w / 2 + dualLeft);
		const top = Math.round(height / 2 - h / 2 + dualTop);
		const win = window.open(href, 'eapShare', 'scrollbars=yes,resizable=yes,width=' + w + ',height=' + h + ',top=' + top + ',left=' + left);
		if (win) {
			if (win.focus) {
				win.focus();
			}
		} else {
			window.open(href, '_blank', 'noopener');
		}
	};

	const fallbackCopy = (text, done) => {
		try {
			const ta = document.createElement('textarea');
			ta.value = text;
			ta.setAttribute('readonly', '');
			ta.style.position = 'absolute';
			ta.style.left = '-9999px';
			document.body.appendChild(ta);
			ta.select();
			document.execCommand('copy');
			document.body.removeChild(ta);
			done();
		} catch (e) {}
	};

	const copyLink = (btn) => {
		const url = btn.getAttribute('data-eap-share-url') || window.location.href;
		const done = () => {
			btn.classList.add('is-copied');
			window.setTimeout(() => btn.classList.remove('is-copied'), 1500);
		};
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(url).then(done).catch(() => fallbackCopy(url, done));
		} else {
			fallbackCopy(url, done);
		}
	};

	const setup = (widget, editorMode) => {
		if (widget.dataset.eapSsBound === '1') {
			return;
		}
		widget.dataset.eapSsBound = '1';

		const shareSupported = typeof navigator !== 'undefined' && typeof navigator.share === 'function';
		const buttons = widget.querySelectorAll('.eap-social-share__btn');

		buttons.forEach((btn) => {
			const type = btn.getAttribute('data-eap-share');

			// The native "Share…" button only exists where the Web Share API is
			// available; reveal it there (and in the editor so it can be styled).
			if (type === 'native' && (shareSupported || editorMode)) {
				btn.classList.add('is-share-supported');
			}

			// Never fire real share actions inside the Elementor editor.
			if (editorMode) {
				return;
			}

			if (type === 'popup') {
				btn.addEventListener('click', (event) => {
					const href = btn.getAttribute('href');
					if (!href || href === '#') {
						return;
					}
					event.preventDefault();
					openPopup(href);
				});
			} else if (type === 'copy') {
				btn.addEventListener('click', (event) => {
					event.preventDefault();
					copyLink(btn);
				});
			} else if (type === 'print') {
				btn.addEventListener('click', (event) => {
					event.preventDefault();
					window.print();
				});
			} else if (type === 'native') {
				btn.addEventListener('click', (event) => {
					event.preventDefault();
					if (!navigator.share) {
						return;
					}
					navigator.share({
						title: btn.getAttribute('data-eap-share-title') || document.title,
						url: btn.getAttribute('data-eap-share-url') || window.location.href,
					}).catch(() => {});
				});
			}
			// 'mailto' is left as a normal link.
		});
	};

	const run = (root, editorMode) => {
		const nodes = (api && typeof api.getNodes === 'function')
			? api.getNodes(root, SELECTOR)
			: Array.from((root || document).querySelectorAll(SELECTOR));
		nodes.forEach((widget) => setup(widget, editorMode));
	};

	if (api && typeof api.register === 'function') {
		api.register('social-share', (root, options = {}) => run(root, !!options.editorMode));
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => run(document, false));
	} else {
		run(document, false);
	}
})();
