<?php

require_once get_template_directory() . '/lib/helpers/BlockFileGenerator.php';

class rpmblocks
{
	public $customBlocks;
	protected $customBlockCategories;

	public function __construct()
	{
		$this->customBlockCategories = [
			[
				'slug' => 'custom-blocks',
				'title' => __('Custom Blocks', 'custom-blocks')
			]
		];

		$this->customBlocks = [
			[
				'name'            => 'banner',
				'title'           => __('Banner'),
				'description'     => __('A custom banner block.'),
				'render_template' => get_template_directory() . '/blocks/banner/banner.php',
				'category'        => 'custom-blocks',
				'icon'            => 'format-image',
				'keywords'        => ['banner', 'display'],
				'supports' => [
					'align' => false
				]
			],
			[
				'name'            => 'columns',
				'title'           => __('Columns'),
				'description'     => __('A custom columns block.'),
				'render_template' => get_template_directory() . '/blocks/columns/columns.php',
				'category'        => 'custom-blocks',
				'icon'            => 'tagcloud',
				'keywords'        => ['col', 'column', 'columns'],
				'supports' => [
					'align' => false
				]
			],
			[
				'name'            => 'text',
				'title'           => __('Text'),
				'description'     => __('A custom text block.'),
				'render_template' => get_template_directory() . '/blocks/text/text.php',
				'category'        => 'custom-blocks',
				'icon'            => 'editor-textcolor',
				'keywords'        => ['text', 'editor', 'heading', 'paragraph', 'content'],
				'supports' => [
					'align' => false
				]
			]
		];

		$this->init();
	}

	/**
	 * Initialize our block logic (check that ACF has the functionality needed and register hooks)
	 *
	 * @return void
	 */
	public function init()
	{
		if (!function_exists('acf_register_block_type')) return;

		add_action('acf/init', [$this, 'register_acf_block_types']);
		add_action('acf/field_group/admin_head', [$this, 'generate_block_files']);
		add_filter('allowed_block_types', [$this, 'allowed_block_types']);
		add_filter('block_categories',  [$this, 'register_custom_block_categories'], 10, 2);
	}

	/**
	 * Cf. https://www.advancedcustomfields.com/resources/acf_register_block_type/
	 *
	 * @return void
	 */
	public function register_acf_block_types()
	{
		foreach ($this->customBlocks as $block) {
			acf_register_block_type($block);
		}
	}

	/**
	 * Restrict the default available block types
	 * cf. https://wordpress.stackexchange.com/a/326963
	 *
	 * @param Array $allowedBlocks
	 * @return void
	 */
	public function allowed_block_types($allowedBlocks)
	{
		$allowedBlocks = [
			// 'core/paragraph',
			// 'core/heading',
			// 'core/list'
		];

		foreach ($this->customBlocks  as $block) {
			$allowedBlocks[] = 'acf/' . $block['name'];
		}

		return $allowedBlocks;
	}

	/**
	 * Generate all the necessary files for our custom blocks
	 *
	 * @return void
	 */
	public function generate_block_files()
	{
		foreach ($this->customBlocks as $block) {
			$generator = new BlockFileGenerator($block['name']);
			$generator->generate();
		}
	}

	/**
	 * Add custom categories to the blocks list
	 *
	 * @param array $categories Array of block categories
	 * @param WP_Post $post Post being loaded
	 * @return array
	 */
	public function register_custom_block_categories($categories, $post)
	{
		return array_merge($categories, $this->customBlockCategories);
	}
}
