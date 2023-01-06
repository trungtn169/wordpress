function callback($block_content, $block)
{
	$block_class = $block['attrs']['className'];
	if (!empty($block['attrs']['className']) && str_contains($block_class, 'class_to_check')) {
		// echo '<pre>';
		// var_dump($block);
		// echo '</pre>';
	}

	return $block_content;
}
add_filter('render_block', 'callback', 10, 2);
