(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	const initAdvancedAnimatedText = (root) => {
		api.getNodes(root, '[data-eap-advanced-text]').forEach((element) => {
			if (element.dataset.eapAdvancedTextInit === 'true') {
				return;
			}

			element.dataset.eapAdvancedTextInit = 'true';
			const terms = JSON.parse(element.dataset.eapAdvancedText || '[]');
			const effect = element.dataset.eapAdvancedTextEffect || 'typewriter';
			const output = element.querySelector('.eap-advanced-animated-text__dynamic');
			const speed = parseInt(element.dataset.eapSpeed || '90', 10);
			const pause = parseInt(element.dataset.eapPause || '1500', 10);

			if (!terms.length || !output || api.prefersReducedMotion()) {
				if (output && terms[0]) {
					output.textContent = terms[0];
				}
				return;
			}

			if ('changing' === effect) {
				let index = 0;
				window.setInterval(() => {
					index = (index + 1) % terms.length;
					output.classList.remove('is-changing');
					window.requestAnimationFrame(() => {
						output.textContent = terms[index];
						output.classList.add('is-changing');
					});
				}, Math.max(pause, 400));
				return;
			}

			if ('typewriter' !== effect) {
				return;
			}

			let termIndex = 0;
			let charIndex = 0;
			let deleting = false;

			const tick = () => {
				const current = terms[termIndex] || '';
				output.textContent = current.slice(0, charIndex);

				if (!deleting) {
					charIndex += 1;
					if (charIndex > current.length) {
						deleting = true;
						window.setTimeout(tick, pause);
						return;
					}
				} else {
					charIndex -= 1;
					if (charIndex < 0) {
						deleting = false;
						termIndex = (termIndex + 1) % terms.length;
						charIndex = 0;
					}
				}

				window.setTimeout(tick, Math.max(speed, 25));
			};

			tick();
		});
	};

	api.register('advanced-animated-text', (root) => {
		initAdvancedAnimatedText(root);
	});
})();
