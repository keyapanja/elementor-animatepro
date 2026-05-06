(() => {
	const api = window.EAPFrontend;
	if (!api) {
		return;
	}

	api.register('visibility', (root, { editorMode = false } = {}) => {
		api.observeElements(root, '.eap-motion,.eap-nav-animate,.eap-animated-text[data-eap-split-trigger="scroll"],.eap-animated-text-v2[data-eap-animated-text-v2="true"],.eap-text-mask-reveal[data-eap-mask-trigger="scroll"],.eap-image-box,.eap-image-hotspot,.eap-image-widget,.eap-icon-box,.eap-testimonial-box,.eap-advanced-button-wrap');

		if (editorMode) {
			api.getNodes(root, '.eap-motion,.eap-nav-animate,.eap-animated-text,.eap-animated-text-v2,.eap-text-mask-reveal,.eap-image-box,.eap-image-hotspot,.eap-image-widget,.eap-icon-box,.eap-testimonial-box,.eap-advanced-button-wrap').forEach((element) => {
				element.classList.add('is-visible', 'is-animated');
			});
		}
	});
})();
