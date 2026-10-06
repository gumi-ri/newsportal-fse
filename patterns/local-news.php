<?php
/**
 * Title: 综合资讯
 * Slug: newsportal/local-news
 * Categories: newsportal
 * Description: 通栏「综合资讯 NEWS」三列：焦点资讯(列表) / 新闻图片(大图) / 更多资讯(列表)。分类由站点自行创建后在编辑器「筛选条件」中绑定。
 * Keywords: 资讯, 新闻, 板块
 */
$np_bendi  = get_term_by( 'slug', 'bendi', 'category' );
$np_bendi  = $np_bendi ? (int) $np_bendi->term_id : 0;
$np_tupian = get_term_by( 'slug', 'tupian', 'category' );
$np_tupian = $np_tupian ? (int) $np_tupian->term_id : 0;
$np_remen  = get_term_by( 'slug', 'remen', 'category' );
$np_remen  = $np_remen ? (int) $np_remen->term_id : 0;
?>
<!-- wp:group {"className":"np-section np-local-wrap","layout":{"type":"constrained"}} -->
<div class="wp-block-group np-section np-local-wrap">

<!-- wp:group {"className":"np-sec-head","layout":{"type":"flex","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group np-sec-head">
<!-- wp:heading {"level":2,"className":"np-sec-title"} -->
<h2 class="wp-block-heading np-sec-title">综合资讯 <span class="np-sec-en">NEWS</span></h2>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"np-local-cols","layout":{"type":"default"}} -->
<div class="wp-block-group np-local-cols">
<!-- wp:group {"className":"np-local-focus-col","layout":{"type":"default"}} -->
<div class="wp-block-group np-local-focus-col">
<!-- wp:heading {"level":3,"className":"np-sub-title"} -->
<h3 class="wp-block-heading np-sub-title">焦点资讯</h3>
<!-- /wp:heading -->
<!-- wp:query {"queryId":25,"query":{"perPage":8,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"category":[<?php echo (int) $np_bendi; ?>]}},"layout":{"type":"default"}} -->
<div class="wp-block-query np-local-focus">
<!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:post-title {"isLink":true,"className":"np-list-title"} /-->
<!-- /wp:post-template -->
</div>
<!-- /wp:query -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"np-local-zixun-col","layout":{"type":"default"}} -->
<div class="wp-block-group np-local-zixun-col">
<!-- wp:heading {"level":3,"className":"np-sub-title"} -->
<h3 class="wp-block-heading np-sub-title">新闻资讯</h3>
<!-- /wp:heading -->
<!-- wp:query {"queryId":27,"query":{"perPage":8,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"category":[<?php echo (int) $np_remen; ?>]}},"layout":{"type":"default"}} -->
<div class="wp-block-query np-local-zixun">
<!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:post-title {"isLink":true,"className":"np-list-title"} /-->
<!-- /wp:post-template -->
</div>
<!-- /wp:query -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"np-local-pic-col","layout":{"type":"default"}} -->
<div class="wp-block-group np-local-pic-col">
<!-- wp:heading {"level":3,"className":"np-sub-title"} -->
<h3 class="wp-block-heading np-sub-title">新闻图片</h3>
<!-- /wp:heading -->
<!-- wp:query {"queryId":26,"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"category":[<?php echo (int) $np_tupian; ?>]}},"layout":{"type":"default"}} -->
<div class="wp-block-query np-local-pic">
<!-- wp:post-template {"layout":{"type":"default"}} -->
<!-- wp:group {"className":"np-local-pic-item","layout":{"type":"default"}} -->
<div class="wp-block-group np-local-pic-item">
<!-- wp:post-featured-image {"isLink":true,"scale":"cover"} /-->
<!-- wp:post-title {"isLink":true,"className":"np-local-pic-title"} /-->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->
</div>
<!-- /wp:query -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
