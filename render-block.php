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

// add_featured_post_navigation
function register_image_size()
{
  add_image_size('post-navigation-thumb', 220);
}
add_action('after_setup_theme', 'register_image_size');

function get_href($str){
  $dom = new domDocument;
  @$dom->loadHTML($str);
  $tag_a = $dom->getElementsByTagName('a');

  foreach ($tag_a as $a){
    $href = $a->getAttribute('href');
  }

  return $href;
}

function add_featured_post_navigation($block_content, $block)
{
  $block_class = $block['attrs']['className'];

  if (!empty($block['attrs']['className']) && str_contains($block_class, 'post_navigation')) {
    $block_content_origin = $block_content;
    $link_post = get_href($block_content);
    if($link_post){
      $postID = url_to_postid(get_href($block_content));
      $img = get_the_post_thumbnail($postID, 'post-navigation-thumb');
      $featured_img = '<figure><a href="' . $link_post . '">' . $img . '</a></figure>';
      $block_content = '<div class="wp-block-group">' . $featured_img . $block_content_origin . '</div>';
    }
  }

  return $block_content;
}
add_filter('render_block', 'add_featured_post_navigation', 10, 2);
