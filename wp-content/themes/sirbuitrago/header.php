<?php
	$head_scripts = get_field('head_scripts', 'option') ?? '';
	$body_scripts = get_field('body_scripts', 'option') ?? '';
?>
<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<?php wp_head(); ?>
	<?= $head_scripts; ?>
</head>

<body <?= body_class(); ?>>

	<?= $body_scripts; ?>

	<a href="#rpmMainContent" class="skip_to_main_link"><?= get_field('skip_content_label', 'option') ?></a>

	<?= get_template_part('/inc/components/gdpr'); ?>

	<?= get_template_part('/inc/components/site', 'header'); ?>

	<main id="rpmMainContent">
