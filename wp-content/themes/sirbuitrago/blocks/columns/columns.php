<?php

/**
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param boolean $is_preview True during AJAX preview.
 * @param integer $post_id The post ID this block is saved to.
 */
$id = $block['name'] . '-' . $block['id'];
$blockName = str_replace('acf/', '', $block['name']);
$classes = 'rpmblock rpmblock--' . $blockName;
$blockFields = get_fields();

if (!empty($block['className'])) $classes .= ' ' . $block['className'];
if (!empty($block['align'])) $classes .= ' align' . $block['align'];
if (!empty($blockFields['block_id'])) $id = str_replace(' ', '-', strtolower($blockFields['block_id']));
?>

<div id="<?= $id ?>" class="<?= $classes ?>">

	<?php if ($is_preview) : ?>
		<span class="block-badge"><?= $block['title'] ?></span>
	<?php endif; ?>

	<?php
		$cols = 1;
		$max_cols = 6;

		if( $blockFields['column_count'] == 'auto'){
			$cols = count($blockFields['columns']);
			$cols = $cols > $max_cols ? $max_cols : $cols;
			$cols = $cols < 1 ? 1 : $cols;
		} else {
			$cols = $blockFields['column_count'];
		}
	?>

	<div class="container">
		<div class="col-container columns--<?= $cols ?>">

			<?php
			if ($blockFields['columns']) :
				foreach ($blockFields['columns'] as $col) :
				?>

					<div class="col">
						<div class="col__inner">
							<?php
								if ($col['image']){
									echo'<div class="col__image">'.$col['image']['html'].'</div>';
								}

								if ($col['content']){
									echo'<div class="col__content">'.$col['content'].'</div>';
								}
							?>
						</div>
					</div>

				<?php
				endforeach;
			endif;
			?>

		</div>
	</div>
</div>
