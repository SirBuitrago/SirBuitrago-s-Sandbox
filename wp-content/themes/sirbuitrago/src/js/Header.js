export const Header = (function () {
	'use strict';

	let $menu, $menuButton, $subMenuParent, $subMenuItem, $body;

	function init() {
		($menu = $('.site-header__nav .nav-items')), ($menuButton = $('.mobile-menu-button'));
		$body = $('body');
		$subMenuParent = $('.site-header .menu-item-has-children');
		$subMenuItem = $('.site-header .sub-menu a');

		events();
	}

	function events() {
		$menuButton.click(function () {
			if ($menu.hasClass('open')) {
				$body.removeClass('mobile-menu-open');
				$menu.removeClass('open');
				$menuButton.removeClass('open').attr('aria-expanded', 'false');
			} else {
				$body.addClass('mobile-menu-open');
				$menu.addClass('open');
				$menuButton.addClass('open').attr('aria-expanded', 'true');
			}
		});
	}

	return {
		init: init,
	};
})();
