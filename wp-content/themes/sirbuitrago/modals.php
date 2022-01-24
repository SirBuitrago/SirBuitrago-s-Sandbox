<?php
	$modals = get_field('modals', 'options');

	if ($modals):
		foreach ($modals as $modal):
			$ID = $modal['modal_id'];
			$modalID = str_replace(' ', '-', strtolower($ID));
			$modalContent = $modal['modal_content'];
			?>

				<div id="<?= $modalID ?>" aria-hidden="true" class="modal">
					<div class="modal__bg" tabindex="-1" data-micromodal-close></div>
					<div  class="modal__content" role="dialog" aria-modal="true" aria-labelledby="<?= $modalID ?>">
				    <header>
				      <h2 id="<?= $modalID ?>-title">
				        Modal Title
				      </h2>
				      <span class="modal__close" aria-label="Close modal" data-micromodal-close>Close Modal</span>
				    </header>

				    <div id="<?= $modalID ?>-content">
							<?= $modalContent ?>
				    </div>
				  </div>
				</div>

			<?php
		endforeach;
	endif;
?>
