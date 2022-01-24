<?php get_header(); ?>

	<div class="rpmblock rpmblock--text">
		<div class="container">
			<div class="wysiwyg">
				<?php
					$error_404_text = get_field('error_404_text', 'options');
					if ($error_404_text) {
						echo $error_404_text;
					}
				 ?>
			</div>
		</div>
	</div>

<?php get_footer(); ?>
