<?php
/**
 * Title: 相关文章
 * Slug: newsportal/related-posts
 * Categories: newsportal
 * Description: 文章页底部「相关文章」，自动按当前文章的第一个分类拉取 5 条（编辑器预览时回退为最新文章）。
 * Keywords: 相关, 文章
 */
$np_related_id = 0;
if ( is_singular() ) {
	$np_post = get_queried_object();
	if ( $np_post instanceof WP_Post ) {
		$np_cats = get_the_category( $np_post->ID );
		if ( ! empty( $np_cats ) ) {
			$np_related_id = (int) $np_cats[0]->term_id;
		}
	}
}
?>
<!-- wp:group {"className":"np-section np-related-wrap","layout":{"type":"constrained"}} -->
<div class="wp-block-group np-section np-related-wrap">
<!-- wp:heading {"level":2,"className":"np-sec-title"} -->
<h2 class="wp-block-heading np-sec-title">相关文章</h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":7,"query":{"perPage":5,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false<?php if ( $np_related_id ) : ?>,"taxQuery":{"category":[<?php echo (int) $np_related_id; ?>]}<?php endif; ?>},"layout":{"type":"default"}} -->
<div class="wp-block-query np-related-list">
<!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:post-title {"isLink":true,"level":3,"className":"np-list-title"} /-->
<!-- /wp:post-template -->
</div>
<!-- /wp:query -->
</div>
<!-- /wp:group -->
