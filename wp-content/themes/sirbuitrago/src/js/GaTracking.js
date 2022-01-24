export const GaTracking = (function () {
	'use strict';

	let eventData;

	function init() {
		events();
	}

	function events() {
		$('body').on('click', 'a[data-category]', function () {
			let $t = $(this),
				cat = $t.data('category'),
				label = $t.data('label');

			gaClickHandler(cat, label);
		});
	}

	function gaClickHandler(cat, label) {
		if (gtag) {
			gtag('event', 'click', {
				event_category: cat,
				event_label: label,
			});
		}

		console.warn('Sent a GA event: ', '\n', '-- Category: ' + cat, '\n', '-- Label: ' + label, '\n');
	}

	return {
		init: init,
	};
})();
