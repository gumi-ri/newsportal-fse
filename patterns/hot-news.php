<?php
/**
 * Title: 热点要闻
 * Slug: newsportal/hot-news
 * Categories: newsportal
 * Description: 左栏「热点要闻」区：顶部刷新提示 + Tab + 红点头条列表（热点分类）+ 蓝点焦点列表（推荐分类）。
 * Keywords: 热点, 要闻, 头条
 */
$np_redian  = get_term_by( 'slug', 'redian', 'category' );
$np_redian  = $np_redian ? (int) $np_redian->term_id : 0;
$np_tuijian = get_term_by( 'slug', 'tuijian', 'category' );
$np_tuijian = $np_tuijian ? (int) $np_tuijian->term_id : 0;
?>
<!-- wp:group {"className":"np-section np-hot-news-wrap","layout":{"type":"constrained"}} -->
<div class="wp-block-group np-section np-hot-news-wrap">

<!-- wp:group {"className":"np-tab","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group np-tab">
<!-- wp:paragraph {"className":"np-tab-item np-tab-active"} -->
<p class="np-tab-item np-tab-active">热点要闻</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:query {"queryId":21,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"category":[<?php echo (int) $np_redian; ?>]}},"layout":{"type":"default"}} -->
<div class="wp-block-query np-hotnews">
<!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:post-title {"isLink":true} /-->
<!-- /wp:post-template -->
</div>
<!-- /wp:query -->

<!-- wp:query {"queryId":22,"query":{"perPage":8,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"category":[<?php echo (int) $np_tuijian; ?>]}},"layout":{"type":"default"}} -->
<div class="wp-block-query np-focuslist">
<!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:post-title {"isLink":true} /-->
<!-- /wp:post-template -->
</div>
<!-- /wp:query -->

</div>
<!-- /wp:group -->
