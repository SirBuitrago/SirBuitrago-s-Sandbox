<?php

/**
 * Generate the files needed for our custom blocks
 */
class BlockFileGenerator
{
	protected $blockType;
	protected $blockName;

	public function __construct(String $blockSlug)
	{
		$this->blockType = $blockSlug;

		$this->blockName = array_reduce(explode('-', $this->blockType), function ($carry, $item) {
			$carry .= ucfirst($item);
			return $carry;
		});
	}

	public function generate()
	{
		$blockDirPath = get_template_directory() . '/blocks/' . $this->blockType;

		if (is_dir($blockDirPath)) return;

		mkdir($blockDirPath, 0777, true);
		file_put_contents($blockDirPath . '/' . $this->blockType . '.scss', $this->get_template(get_template_directory() . '/src/templates/block-template.scss'));
		file_put_contents($blockDirPath . '/' . $this->blockType . '.php', $this->get_template(get_template_directory() . '/src/templates/block-template.php'));
		file_put_contents($blockDirPath . '/' . $this->blockType . '.js', $this->get_template(get_template_directory() . '/src/templates/block-template.js'));
	}

	protected function get_template($templatePath)
	{
		return str_replace(['*RPM_BLOCK_TYPE*', '*RPM_BLOCK_NAME*'], [$this->blockType, $this->blockName], file_get_contents($templatePath));
	}
}
