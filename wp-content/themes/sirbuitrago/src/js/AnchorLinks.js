export const AnchorLinks = (function () {
	'use strict';

	let $win, $anchorLink;

	function init() {
		$win = jQuery(window);
		$anchorLink = $('a[href^="#"]');

		if (location.hash) {
			if ($(location.hash).length > 0 && !$(location.hash).hasClass('modal') ) {
				scrollToEl(location.hash);
			}
		}

		$anchorLink.click(function (e) {
			let id = $(this).attr('href');

			if (id.length > 1 && $(id).length > 0 && !$(id).hasClass('modal') ) {
				e.preventDefault();
				scrollToEl(id);
			}

		});
	}

	function scrollToEl(id) {
		$('html, body').animate(
			{
				scrollTop: $(id).offset().top,
			},
			1000
		);
	}

	return {
		init: init,
	};
})();
