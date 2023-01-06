function menu_post_per_page($query)
{
	global $ptm_options_posts_per_page;
	if (!$query->is_main_query() || is_admin()) return;

	if (is_tax('cat_menu')) {
		$query->set('posts_per_page', (int)$ptm_options_posts_per_page);
	}

	return $query;
}
add_filter('pre_get_posts', 'menu_post_per_page');
