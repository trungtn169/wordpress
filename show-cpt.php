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

// Get list child of post type
function categories_child($atts = array())
{
	$args = array(
		'taxonomy' => $atts['taxonomy'],
		'orderby' => 'id',
		'order'   => 'ASC',
		'hide_empty' => false,
	);
	$categories = get_categories($args);
	$taxonomy = get_queried_object()->taxonomy;

	$isCPT = get_queried_object()->name == $atts['post_type'];
	$isCatCPT = $taxonomy == $atts['taxonomy'];

	$className = "current-cat";
	$class_current = $isCPT ? $className : "";

	$current_id = get_queried_object()->term_id;

	$output = '';
	$output .= '<ul class="list-categories-child">';
	$output .= '<li class="all ' . $class_current . '"><a href="' . get_post_type_archive_link($atts['post_type']) . '">すべて</a></li>';
	$class_current = $isCatCPT ? $className : "";
	foreach ($categories as $category) {
		$class_current = '';
		if ($category) {
			if ($current_id == $category->term_id) $class_current .= $className;
			$output .= '<li class="category cat-item cat-item-' . $category->term_id . ' ' . $class_current . '"><a href="' . get_category_link($category->term_id) . '" title="' . $category->name . '" ' . '><span>' . $category->name . '</span></a>';
		}
	}
	$output .= '</li>';
	$output .= '</ul>';

	return $output;
}
add_shortcode('categories_child', 'categories_child');
