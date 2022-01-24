export const Modals = (function () {
	'use strict';

	let $a;

	function init() {

		$a = $('a[href^="#"]');

		MicroModal.init({
			onShow: modal => {
				history.replaceState(null, null, `#${modal.id}`);
				document.body.classList.add('modal--is-open');
			},
			onClose: modal => {
				history.replaceState(null, null, ' ');
				document.body.classList.remove('modal--is-open');
			}
		});

		document.querySelectorAll('[data-micromodal-trigger]').forEach(item => {
		  item.addEventListener('click', e => {
				e.preventDefault();
		  })
		})

		$a.click( function(){
			let href = $(this).attr('href');
			if ( $(href+'.modal').length ) {
				MicroModal.show( href.replace('#', ''), {
					onClose: modal => {
						history.replaceState(null, null, ' ');
						document.body.classList.remove('modal--is-open');
					}
				});
			}
		})

		if(window.location.hash) {
			var hash = window.location.hash;

			if ( $(hash+'.modal').length ) {

				document.body.classList.add('modal--is-open');

				MicroModal.show( hash.replace('#', ''), {
					onClose: modal => {
						history.replaceState(null, null, ' ');
						document.body.classList.remove('modal--is-open');
					}
				});
			}
		}

	}

	return {
		init: init,
	};
})();
