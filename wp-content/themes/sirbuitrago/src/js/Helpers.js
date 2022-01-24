import { throttle } from 'lodash-es';
import Cookies from 'js-cookie';

export const Helpers = (function () {
	'use strict';

	function init() {
		cookie_banner();
		responsive_videos();
		body_scroll();
		accordions();
	}

	function cookie_banner() {
		let banner = document.querySelector('.cookie-banner');

		if (!banner) return;

		banner.querySelectorAll('button').forEach((btn) => {
			btn.addEventListener('click', (ev) => {
				ev.preventDefault();

				let acceptance = btn.classList.contains('accept') ? 'yes' : 'no';

				Cookies.set('acceptedCookiePrompt', acceptance);
				window.location.reload();
			});
		});
	}

	function responsive_videos() {
		let responsiveVideos = document.querySelectorAll('.responsive-video');

		if (!responsiveVideos) return;

		responsiveVideos.forEach((vid) => {
			let playButton = vid.querySelector('.play-button');
			let videoEl = vid.querySelector('video');
			let iframe = vid.querySelector('iframe');
			let cover = vid.querySelector('.responsive-video__cover');

			if (!playButton) return;

			if (iframe) {
				let iframeOriginalSrc = iframe.getAttribute('src');

				iframe.setAttribute('src', iframe.getAttribute('src') + '&showinfo=0&controls=0');
			}

			playButton.addEventListener('click', (ev) => {
				vid.classList.add('playing');
			});

			playButton.addEventListener('transitionend', (ev) => {
				playButton.parentNode.removeChild(playButton);

				if (cover) {
					cover.parentNode.removeChild(cover);
				}

				if (videoEl) {
					videoEl.setAttribute('controls', 'controls');
					videoEl.play();
				}

				if (iframe) {
					iframe.setAttribute('src', iframe.getAttribute('src') + '&autoplay=1');
				}
			});
		});
	}

	function body_scroll() {
		window.addEventListener(
			'scroll',
			throttle(() => {
				window.scrollY > 100 ? document.body.classList.add('scrolled') : document.body.classList.remove('scrolled');
			}, 150)
		);
	}

	function getUrlParams(search) {
		if (search.length <= 1) return {};
		const hashes = search.slice(search.indexOf('?') + 1).split('&');
		const params = {};
		hashes.map((hash) => {
			const [key, val] = hash.split('=');
			params[key] = decodeURIComponent(val);
		});
		return params;
	}

	function accordions() {
		let accordions = Array.from(document.querySelectorAll('[data-accordion]'));

		if (accordions.length === 0) return;

		accordions.forEach((accordion) => {
			let trigger = accordion.querySelector('[data-accordion-trigger]');

			trigger.addEventListener('click', (ev) => {
				accordion.classList.toggle('accordion-open');
			});
		});
	}

	return {
		init: init,
		getUrlParams: getUrlParams,
	};
})();
