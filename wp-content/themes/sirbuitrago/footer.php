<?php
	$footer_scripts = get_field('footer_scripts', 'option') ?? '';
?>
	</main>

	<?= get_template_part('/template-parts/components/site', 'footer'); ?>

	<?php wp_footer(); ?>
</body>

<?php include('modals.php') ?>
<?= $footer_scripts; ?>

</html>
