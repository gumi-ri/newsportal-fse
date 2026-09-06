<?php
/**
 * Title: 推荐信息流
 * Slug: newsportal/feed
 * Categories: newsportal
 * Description: 左栏推荐信息流，带 95×66 缩略图（推荐分类，跳过前 8 条与焦点列表错开），底部「更多个性推荐新闻」按钮。
 * Keywords: 推荐, 信息流, feed
 */
$np_tuijian = get_term_by( 'slug', 'tuijian', 'category' );
$np_tuijian = $np_tuijian ? (int) $np_tuijian->term_id : 0;
?>
<!-- wp:group {"className":"np-section np-feed-wrap","anchor":"np-feed","layout":{"type":"constrained"}} -->
<div class="wp-block-group np-section np-feed-wrap" id="np-feed">
<!-- wp:query {"queryId":23,"query":{"perPage":10,"pages":0,"offset":8,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"category":[<?php echo (int) $np_tuijian; ?>]}},"layout":{"type":"default"}} -->
<div class="wp-block-query np-feed">
<!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:group {"className":"np-feed-item","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group np-feed-item">
<!-- wp:post-featured-image {"isLink":true,"scale":"cover","width":"95px","height":"66px"} /-->
<!-- wp:group {"className":"np-feed-text","layout":{"type":"default"}} -->
<div class="wp-block-group np-feed-text">
<!-- wp:post-title {"isLink":true} /-->
<!-- wp:post-date {"format":"Y-m-d H:i","className":"np-item-meta"} /-->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"className":"np-empty"} -->
<p class="np-empty">暂无内容，请先在「推荐」分类下发布文章。</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->

<!-- wp:paragraph {"className":"np-feed-more"} -->
<p class="np-feed-more"><a href="/category/tuijian/">更多个性推荐新闻</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
