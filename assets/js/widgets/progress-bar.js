(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const RADIUS = 50;
	const CIRCUMFERENCE = 2 * Math.PI * RADIUS;
	let fallbackObserver = null;

	const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

	const setValueText = (widget, progress) => {
		widget.querySelectorAll('[data-eap-progress-value-text]').forEach((node) => {
			node.textContent = `${Math.round(progress)}%`;
		});
	};

	const setProgressState = (widget, progress) => {
		const value = clamp(progress, 0, 100);
		const layout = widget.dataset.eapLayout || 'flat';

		if (layout === 'circular') {
			const ring = widget.querySelector('[data-eap-progress-ring]');
			if (ring) {
				ring.style.strokeDasharray = String(CIRCUMFERENCE);
				ring.style.strokeDashoffset = String(CIRCUMFERENCE * (1 - value / 100));
			}
		} else {
			const fill = widget.querySelector('[data-eap-progress-fill]');
			if (fill) {
				fill.style.width = `${value}%`;
			}
		}

		setValueText(widget, value);
	};

	const animateWidget = (widget, targetValue) => {
		const duration = parseInt(widget.dataset.eapDuration || '1200', 10);
		const delay = parseInt(widget.dataset.eapDelay || '0', 10);
		const start = performance.now() + delay;
		const from = parseFloat(widget.dataset.eapCurrent || '0');
		const to = clamp(targetValue, 0, 100);

		widget.style.setProperty('--eap-progress-duration', `${duration}ms`);

		if (widget._eapProgressFrame) {
			cancelAnimationFrame(widget._eapProgressFrame);
		}

		const step = (now) => {
			if (now < start) {
				widget._eapProgressFrame = requestAnimationFrame(step);
				return;
			}

			const elapsed = Math.min((now - start) / Math.max(duration, 1), 1);
			const eased = 1 - Math.pow(1 - elapsed, 3);
			const current = from + ((to - from) * eased);

			widget.dataset.eapCurrent = String(current);
			setProgressState(widget, current);

			if (elapsed < 1) {
				widget._eapProgressFrame = requestAnimationFrame(step);
				return;
			}

			widget.dataset.eapCurrent = String(to);
			widget._eapProgressFrame = null;
		};

		widget._eapProgressFrame = requestAnimationFrame(step);
	};

	const bindWidget = (widget, editorMode) => {
		if (!widget || widget.dataset.eapProgressBound === 'true') {
			if (editorMode && widget) {
				const targetValue = parseFloat(widget.dataset.eapValue || '0');
				setProgressState(widget, targetValue);
				widget.dataset.eapCurrent = String(targetValue);
			}
			return;
		}

		widget.dataset.eapProgressBound = 'true';
		const targetValue = clamp(parseFloat(widget.dataset.eapValue || '0'), 0, 100);
		setProgressState(widget, editorMode ? targetValue : 0);
		widget.dataset.eapCurrent = String(editorMode ? targetValue : 0);

		if (editorMode) {
			return;
		}

		if (api.ensureScrollTrigger()) {
			window.ScrollTrigger.create({
				trigger: widget,
				start: 'top 85%',
				end: 'bottom 15%',
				onEnter: () => animateWidget(widget, targetValue),
				onEnterBack: () => animateWidget(widget, targetValue),
				onLeave: () => animateWidget(widget, 0),
				onLeaveBack: () => animateWidget(widget, 0)
			});
			return;
		}

		if (!fallbackObserver) {
			fallbackObserver = new IntersectionObserver((entries) => {
				entries.forEach((entry) => {
					const progressWidget = entry.target;
					const progressTarget = clamp(
						parseFloat(progressWidget.dataset.eapValue || '0'),
						0,
						100
					);

					if (entry.isIntersecting) {
						animateWidget(progressWidget, progressTarget);
						return;
					}

					animateWidget(progressWidget, 0);
				});
			}, {
				threshold: 0.2,
				rootMargin: '0px 0px -10% 0px'
			});
		}

		fallbackObserver.observe(widget);
	};

	api.register('progress-bar', (root, { editorMode = false } = {}) => {
		api.getNodes(root, '[data-eap-progress="true"]').forEach((widget) => {
			bindWidget(widget, editorMode);
		});
	});
})();
