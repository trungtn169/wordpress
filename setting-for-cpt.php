// ptm: post type menu
$ptm_page_setting = 'ptm_setting';
$ptm_options_name = 'ptm_options';
$ptm_cat_posts_per_page = 'posts_per_page';

$ptm_options = get_option($ptm_options_name);
$ptm_options_posts_per_page = $ptm_options[$ptm_cat_posts_per_page];

$post_type_company = 'menu';
$taxonomy_cat_company = 'cat_menu';

function ptm_settings_init()
{
	global $ptm_page_setting, $ptm_options_name, $ptm_cat_posts_per_page;
	register_setting($ptm_page_setting, $ptm_options_name);

	add_settings_section(
		'ptm_cat_section',
		__('For Categories', 'twentytwentytwo'),
		null,
		$ptm_page_setting
	);
	add_settings_field(
		$ptm_cat_posts_per_page,
		__('Posts per Page', 'twentytwentytwo'),
		'option_input_text_cb',
		$ptm_page_setting,
		'ptm_cat_section',
		array(
			'label_for' => $ptm_cat_posts_per_page,
		)
	);
}
add_action('admin_init', 'ptm_settings_init');

function option_input_text_cb($args)
{
	global $ptm_options, $ptm_options_name;
	$name = $ptm_options_name . '[' . $args['label_for'] . ']';
?>
	<input type="text" value="<?php echo $ptm_options[$args['label_for']]; ?>" id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr($name); ?>" />
<?php
}

function post_type_menu_setting()
{
	global $ptm_page_setting, $post_type_company;
	add_submenu_page(
		'edit.php?post_type=' . $post_type_company,
		__('Post type Menu Settings', 'twentytwentytwo'),
		__('Settings', 'twentytwentytwo'),
		'manage_options',
		$ptm_page_setting,
		'post_type_menu_setting_html'
	);
}
add_action('admin_menu', 'post_type_menu_setting');

function post_type_menu_setting_html()
{
	global $ptm_page_setting;
	if (!current_user_can('manage_options')) {
		return;
	}

	if (isset($_GET['settings-updated'])) {
		add_settings_error('ptm_message', 'ptm_message', __('Settings Saved', 'twentytwentytwo'), 'updated');
	}

	settings_errors('ptm_message');
?>
	<div class="wrap">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields($ptm_page_setting);
			do_settings_sections($ptm_page_setting);
			submit_button('Save Settings');
			?>
		</form>
	</div>
	<?php
}
