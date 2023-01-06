function block_post_content_args($args, $name)
{
	if ($name == 'core/post-content') {
		$args['render_callback'] = 'modify_core_post_content';
	}
	return $args;
}
add_filter('register_block_type_args', 'block_post_content_args', 10, 3);

function modify_core_post_content($attributes, $content, $block)
{

	static $seen_ids = array();

	if (!isset($block->context['postId'])) {
		return '';
	}

	$post_id = $block->context['postId'];

	if (isset($seen_ids[$post_id])) {
		// WP_DEBUG_DISPLAY must only be honored when WP_DEBUG. This precedent
		// is set in `wp_debug_mode()`.
		$is_debug = defined('WP_DEBUG') && WP_DEBUG &&
			defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY;

		return $is_debug ?
			// translators: Visible only in the front end, this warning takes the place of a faulty block.
			__('[block rendering halted]') :
			'';
	}

	$seen_ids[$post_id] = true;

	// Check is needed for backward compatibility with third-party plugins
	// that might rely on the `in_the_loop` check; calling `the_post` sets it to true.
	if (!in_the_loop() && have_posts()) {
		the_post();
	}

	// When inside the main loop, we want to use queried object
	// so that `the_preview` for the current post can apply.
	// We force this behavior by omitting the third argument (post ID) from the `get_the_content`.
	if (str_contains($block->parsed_block['attrs']['className'], 'custom-content')) {
		$content = get_the_content(null, null, $post_id);
	} else {
		$content = get_the_content();
	}
	// Check for nextpage to display page links for paginated posts.
	if (has_block('core/nextpage')) {
		$content .= wp_link_pages(array('echo' => 0));
	}

	/** This filter is documented in wp-includes/post-template.php */
	$content = apply_filters('the_content', str_replace(']]>', ']]&gt;', $content));
	unset($seen_ids[$post_id]);

	if (empty($content)) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes(array('class' => 'entry-content'));

	return ('<div ' . $wrapper_attributes . '>' .
		$content .
		'</div>'
	);
}
