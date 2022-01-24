<?php

require_once __DIR__ . '/RPMBlocks.php';
require_once __DIR__ . '/RPMUtils.php';
require_once __DIR__ . '/helpers/RESTLogic.php';

class RPMGutenbergTheme
{
	protected $rpmblocks;
	protected $shortcodes;

	public function __construct()
	{
		$this->rpmblocks = new rpmblocks();
		$this->shortcodes = [
			'year' => date('Y')
		];
		$this->register_actions();
		$this->register_shortcodes();
		$this->register_filters();
		$this->theme_support();
		$this->add_option_pages();
	}

	public function register_actions()
	{
		add_action('admin_notices', [$this, 'register_admin_notices']);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
		add_action('enqueue_block_editor_assets', [$this, 'enqueue_assets']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
		add_action('wp_head', [$this, 'modify_head']);
		add_action('init', [$this, 'init_hooks']);
		add_action('admin_init', [$this, 'admin_init_hooks']);
		add_action('admin_menu', [$this, 'admin_menu_hooks']);
		add_action('login_enqueue_scripts', [$this, 'login_page_hooks']);
		add_action('rest_api_init', [$this, 'rest_init_hooks']);
	}

	public function register_filters()
	{
		add_filter('acf/format_value/type=image', ['RPMUtils', 'format_acf_images'], 100, 3);
		add_filter('acf/format_value/type=gallery', ['RPMUtils', 'px_format_acf_gallery_images'], 100, 3);
		add_filter('cron_schedules', ['RPMUtils', 'add_30_day_cron_schedule'], 10, 1);
		add_filter('acf/fields/wysiwyg/toolbars', ['RPMUtils', 'modify_acf_wysiwyg_toolbars'], 10, 1);
		add_filter('tiny_mce_before_init', ['RPMUtils', 'modify_tiny_mce_format_options'], 10, 1);
		add_filter('post_thumbnail_html', [$this, 'remove_width_attribute'], 10);
		add_filter('image_send_to_editor', [$this, 'remove_width_attribute'], 10);
		add_filter('wp_prepare_attachment_for_js', [$this, 'common_svg_media_thumbnails'], 10, 3);
		add_filter('block_editor_settings', function ($settings) {
			unset($settings['styles'][0]);
			return $settings;
		});
		add_filter('comments_open', function () {
			return false;
		});
		add_filter('pings_open', function () {
			return false;
		});
		add_filter('comments_array', function ($comments) {
			return [];
		});
		add_filter('excerpt_length', function ($length) {
			return 20;
		}, 999);
		add_filter('excerpt_more', function ($more) {
			return '...';
		});
		add_filter('login_headerurl', function () {
			return home_url();
		});
		add_filter('upload_mimes', function ($mimeTypes) {
			$mimeTypes['svg'] = 'image/svg+xml';
			return $mimeTypes;
		});
		add_filter('the_content', function ($content) {
			return preg_replace('/<p>\s*(<iframe.*>*.<\/iframe>)\s*<\/p>/iU', '<div class="iframe">$1</div>', $content);
		});
		add_filter('wp_mail_content_type', function () {
			return 'text/html';
		});
		// Allowed menu classes
		add_filter('nav_menu_css_class', function ($classes, $item) {
			return is_array($classes) ?
				array_intersect(
					$classes,
					array(
						'current-menu-item',
						'current-menu-parent',
						'menu-item-has-children'
					)
				) : $classes;
		}, 10, 2);

		add_filter('nav_menu_item_id', function () {
			return '';
		}, 100, 1);

		// accessible menus
		add_filter('wp_nav_menu', function ($menu_html, $args) {
			$bad = array('menu', 'navigation', 'nav');
			$menu_label = $args->menu;
			$menu_label = strtolower($menu_label);
			$menu_label = str_replace($bad, '', $menu_label);
			$menu_label = trim($menu_label);
			$menu_html = '<nav aria-label="' . $menu_label . '">' . $menu_html . '</nav>';
			return $menu_html;
		}, 10, 2);

		// limit number of post revisions
		add_filter( 'wp_revisions_to_keep', function($num, $post){
			$revisions = 5;
			return $revisions;
		}, 10, 2 );
	}

	public function common_svg_media_thumbnails($response, $attachment, $meta)
	{
		if ($response['type'] === 'image' && $response['subtype'] === 'svg+xml' && class_exists('SimpleXMLElement')) {
			try {
				$path = get_attached_file($attachment->ID);
				if (@file_exists($path)) {
					$svg = new SimpleXMLElement(@file_get_contents($path));
					$src = $response['url'];
					$width = (int) $svg['width'];
					$height = (int) $svg['height'];

					//media gallery
					$response['image'] = compact('src', 'width', 'height');
					$response['thumb'] = compact('src', 'width', 'height');

					//media single
					$response['sizes']['full'] = array(
						'height'        => $height,
						'width'         => $width,
						'url'           => $src,
						'orientation'   => $height > $width ? 'portrait' : 'landscape',
					);
				}
			} catch (Exception $e) {
			}
		}

		return $response;
	}

	public function remove_width_attribute($html)
	{
		$html = preg_replace('/(width|height)="\d*"\s/', "", $html);
		return $html;
	}

	public function register_shortcodes()
	{
		foreach ($this->shortcodes as $slug => $returnValue) {
			add_shortcode($slug, function ($atts) use ($returnValue) {
				return $returnValue;
			});
		}
	}

	public function enqueue_assets()
	{
		wp_enqueue_style('main', get_template_directory_uri() . '/dist/app.min.css', [], filemtime(get_template_directory() . '/dist/app.min.css'));

		wp_deregister_script('jquery');
		wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', [], null, false);

		wp_enqueue_script('polyfills', 'https://polyfill.io/v3/polyfill.min.js?features=Symbol.iterator,Symbol.isConcatSpreadable,Array.from,Array.prototype.find,NodeList.prototype.forEach,Promise,Object.assign', [], null, true);

		wp_enqueue_script('rpmmodules', get_template_directory_uri() . '/dist/rpmmodules.min.js', [], filemtime(get_template_directory() . '/dist/rpmmodules.min.js'), true);

		wp_enqueue_script('rpmblocks', get_template_directory_uri() . '/dist/rpmblocks.min.js', ['jquery', 'polyfills', 'rpmmodules'], filemtime(get_template_directory() . '/dist/rpmblocks.min.js'), true);

		$whitelist = ['127.0.0.1', '::1', '.test'];	
		if( in_array($_SERVER['REMOTE_ADDR'], $whitelist )){
			$build_react_path = get_template_directory_uri() . '/build/index.js';
			if( file_exists( $build_react_path ) ) {
				wp_enqueue_script('wp-react', $build_react_path, ['wp-element'], filemtime(get_template_directory() . '/build/index.js'), true);
			}
		} else {
			$dist_react_path = get_template_directory_uri() . '/dist/react/index.js';
			if( file_exists( $dist_react_path ) ) {
				wp_enqueue_script('wp-react', $dist_react_path, ['wp-element'], filemtime(get_template_directory() . '/dist/react/index.js'), true);
		
			}
		}

		wp_enqueue_script('main', get_template_directory_uri() . '/dist/app.js', ['rpmblocks'], filemtime(get_template_directory() . '/dist/app.js'), true);

		is_admin() ? wp_localize_script('rpmblocks', 'RPMCustomBlocks', $this->rpmblocks->customBlocks) : wp_localize_script('main', 'RPMConstants', ['ajaxUrl' => admin_url('admin-ajax.php')]);
	}

	public function enqueue_admin_assets()
	{
		// Assets for Admin Only
	}

	public function register_admin_notices()
	{
		RPMUtils::acf_sync_notice();
	}

	public function theme_support()
	{
		add_theme_support('title-tag');
		add_theme_support('menus');
		add_theme_support('post-thumbnails', ['post', 'cast', 'creative']);
		add_theme_support( 'editor-styles');
	}

	public function add_option_pages()
	{
		if (function_exists('acf_add_options_page')) {
			acf_add_options_page([
				'page_title' 	=> 'Theme General Settings',
				'menu_title'	=> 'Theme Settings',
				'menu_slug' 	=> 'theme-general-settings',
				'capability'	=> 'edit_posts',
				'redirect'		=> false
			]);

			acf_add_options_page([
				'page_title' 	=> 'Modals',
				'menu_title'	=> 'Modals',
				'menu_slug' 	=> 'theme-modals',
				'capability'	=> 'edit_posts',
				'icon_url'		=> 'dashicons-editor-expand',
				'redirect'		=> false
			]);
		}
	}

	public function modify_head()
	{
		RPMUtils::add_google_analytics();
	}

	public function init_hooks()
	{
		RPMUtils::register_custom_post_types();
		RPMUtils::clean_head();
	}

	public function admin_init_hooks()
	{
		RPMUtils::disable_comments_logic();
		RPMUtils::add_tinymce_editor_styles();
	}

	public function admin_menu_hooks()
	{
		RPMUtils::cleanup_admin_menu();
	}

	public function login_page_hooks()
	{
		RPMUtils::set_login_page_styles();
	}

	public function rest_init_hooks()
	{
		$R = new RESTLogic();
		$R->register_fields();
		$R->register_routes();
	}
}
