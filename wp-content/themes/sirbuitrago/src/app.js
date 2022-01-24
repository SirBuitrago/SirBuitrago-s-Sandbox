import MicroModal from 'micromodal';

window.addEventListener('DOMContentLoaded', (ev) => {

	$ = jQuery.noConflict();

	for (const key in RPMBlocks) {
		if (RPMBlocks.hasOwnProperty(key)) {
			const block = RPMBlocks[key];
			block.init();
		}
	}

	for (const key in RPMModules) {
		if (RPMModules.hasOwnProperty(key)) {
			const block = RPMModules[key];
			block.init();
		}
	}

	if (undefined !== window.RPMCustomBlocks && window.acf) {
		window.RPMCustomBlocks.forEach((b) => {
			let camelCasedName =
				'RPM' +
				b.name
					.split('-')
					.map((el) => el.charAt(0).toUpperCase() + el.slice(1))
					.join('');

			window.acf.addAction(`render_block_preview/type=${b.name}`, RPMBlocks[camelCasedName].init);
		});
	}
});
