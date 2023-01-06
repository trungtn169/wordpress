// Show CPT in search page
function query_search_cpt($query)
{
	if ($query->is_search) {
		$post_type = array('post', 'case_study', 'news', 'service', 'column', 'member');
		$query->set('post_type', $post_type);
	}
}
add_filter('pre_get_posts', 'query_search_cpt');

// Show post in taxonomy CPT
function query_tax_post_type($query)
{
	if (!$query->is_main_query() || is_admin()) return;

	$cat_post_type = array('cat_menu');
	$isCatCPT = in_array(get_queried_object()->taxonomy, $cat_post_type);

	if ($isCatCPT) {
		$post_type = get_query_var('post_type');
		if ($post_type)
			$post_type = $post_type;
		else
			$post_type = array('menu', 'cat_menu');
		$query->set('post_type', $post_type);
	}

	return $query;
}
add_filter('pre_get_posts', 'query_tax_post_type');
