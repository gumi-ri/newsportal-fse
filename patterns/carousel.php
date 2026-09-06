<?php
/**
 * Title: 大图轮播
 * Slug: newsportal/carousel
 * Categories: newsportal
 * Description: 右栏顶部大图轮播（560×305），自动取「图片」分类最新 3 条特色图，CSS 淡入淡出轮播，标题悬浮于底部遮罩。
 * Keywords: 轮播, 图片, 焦点图
 */
$np_tupian = get_term_by( 'slug', 'tupian', 'category' );
$np_tupian = $np_tupian ? (int) $np_tupian->term_id : 0;
?>
<!-- wp:group {"className":"np-section np-carousel-wrap","layout":{"type":"constrained"}} -->
<div class="wp-block-group np-section np-carousel-wrap">
<!-- wp:query {"queryId":24,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"category":[<?php echo (int) $np_tupian; ?>]}},"layout":{"type":"default"}} -->
<div class="wp-block-query np-carousel">
<!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:group {"className":"np-carousel-item","layout":{"type":"default"}} -->
<div class="wp-block-group np-carousel-item">
<!-- wp:post-featured-image {"isLink":true,"scale":"cover"} /-->
<!-- wp:post-title {"isLink":true,"className":"np-carousel-title"} /-->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"className":"np-empty"} -->
<p class="np-empty">暂无图片，请先在「图片」分类下发布带特色图的文章。</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
</div>
<!-- /wp:group -->
