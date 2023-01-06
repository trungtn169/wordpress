function complex_posts()
{
	ob_start();
	$args = array(
		'post_type'  => array('faq', 'report', 'post'),
		'posts_per_page' => 6,
		'order' => 'DESC',
		'orderby' => 'date',
	);
	$complex_posts = new WP_Query($args);
	if ($complex_posts->have_posts()) { ?>
		<div class="complex_posts">
			<div class="list-posts post-style-5">
				<?php
				while ($complex_posts->have_posts()) {
					$complex_posts->the_post();
				?>
					<div class="wp-block-post">
						<div class="post-group date-post-type">
							<div class="wp-block-post-date">
								<time datetime="<?= get_the_date('c'); ?>"><?= get_the_date('', get_the_ID()); ?></time>
							</div>
							<span class="post-type"><?= getPostType(get_the_ID()); ?></span>
						</div>
						<h3 class="txt-2-line wp-block-post-title">
							<a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php the_title() ?></a>
						</h3>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php }
	wp_reset_query();
	$return = ob_get_clean();

	return $return;
}
add_shortcode('complex_posts', 'complex_posts');

// Popular post
function bgbdj_set_post_views($post_id)
{
	$count_key = 'wp_post_views_count';
	$count = get_post_meta($post_id, $count_key, true);

	if ($count == '') {
		$count = 0;
		delete_post_meta($post_id, $count_key);
		add_post_meta($post_id, $count_key, '0');
	} else {
		$count++;
		update_post_meta($post_id, $count_key, $count);
	}
}

// Shortcode popular post
function bgbdj_track_post_views($post_id)
{
	if (!is_single())  return;

	if (empty($post_id)) {
		global $post;
		$post_id = $post->ID;
	}

	bgbdj_set_post_views($post_id);
}
add_action('wp_head', 'bgbdj_track_post_views');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

function popular_post($atts = array())
{
	ob_start();
	$count = 0;
	$popular_posts_args = array(
		'posts_per_page' => (int)$atts['pots_per_page'],
		'post_type'  => $atts['post_type'],
		'meta_key' => 'wp_post_views_count',
		'orderby' => 'meta_value_num',
		'order' => 'DESC'
	);
	$popular_posts_loop = new WP_Query($popular_posts_args);

	if ($popular_posts_loop->have_posts()) { ?>
		<div class="post_side popular_post <?php echo $atts['post_type'] ?>">
			<h3 class="side-title">人気記事</h3>
			<div class="list-posts post-style-6">
				<?php while ($popular_posts_loop->have_posts()) {
					$popular_posts_loop->the_post();
					$count++;
				?>
					<div class="wp-block-post <?php echo $atts['post_type'] ?>">
						<div class="wp-block-group post-item">
							<?php if (has_post_thumbnail()) : ?>
								<figure class="alignwide wp-block-post-featured-image">
									<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
										<?php the_post_thumbnail('popular_thumb'); ?>
									</a>
									<div class="post-rank rank-<?= $count; ?>">
										<?= $count; ?>
									</div>
								</figure>
							<?php endif; ?>
							<div class="wp-block-group post-item_content">
								<h5 class="alignwide wp-block-post-title">
									<a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php the_title() ?></a>
								</h5>
							</div>
						</div>
					</div>
				<?php
				}
				wp_reset_query(); ?>
			</div>
		</div>
		<?php }
	$return = ob_get_clean();

	return $return;
}
add_shortcode('popular_post', 'popular_post');

function related_post($atts = array())
{
	ob_start();
	$categories = get_the_terms(get_queried_object()->ID, $atts['cat_post_type']);

	if ($categories) {
		$category_ids = array();

		foreach ($categories as $individual_category) $category_ids[] = $individual_category->term_id;

		$args = array(
			'post_type'  => array($atts['post_type']),
			'post__not_in' => array(get_queried_object()->ID),
			'posts_per_page' => (int)$atts['posts_per_page'],
			'order' => 'ASC',
			'orderby' => 'title',
			'tax_query' => array(
				array(
					'taxonomy' => $atts['cat_post_type'],
					'field' => 'id',
					'terms' => $category_ids
				)
			),
		);
		$related_post_loop = new WP_Query($args);

		if ($related_post_loop->have_posts()) { ?>
			<div class="related_posts <?= $atts['post_type'] ?>">
				<h3 class="s-extra-title">その他の導入事例<span>Related</span></h3>
				<div class="list-posts post-style-2">
					<?php
					while ($related_post_loop->have_posts()) {
						$related_post_loop->the_post();
					?>
						<div class="wp-block-post <?= $atts['post_type'] ?>">
							<div class="wp-block-group post-item">
								<?php if (has_post_thumbnail()) : ?>
									<figure class="alignwide wp-block-post-featured-image">
										<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
											<?php the_post_thumbnail('related_thumb'); ?>
										</a>
									</figure>
								<?php endif; ?>
								<div class="wp-block-group post-item_content">
									<h5 class="alignwide wp-block-post-title">
										<a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php the_title() ?></a>
									</h5>
									<div class="wp-block-post-excerpt">
										<div class="wp-block-post-excerpt__excerpt">
											<?php the_excerpt(); ?>
										</div>
									</div>
									<div class="taxonomy-tag_case_study wp-block-post-terms">
										<?php
										$tags = get_the_terms(get_the_ID(), $atts['tag_post_type']);
										foreach ($tags as $tag) { ?>
											<a href="<?php get_tag_link($tag->term_id) ?>" rel="tag" tabindex="0"><?= $tag->name; ?></a>
										<?php
										}
										?>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
<?php }
	}
	wp_reset_query();

	$return = ob_get_clean();

	return $return;
}
add_shortcode('related_post', 'related_post');
